<?php
# * ********************************************************************* *
# *                                                                       *
# *   Login for IDB portals                                               *
# *   This file is part of idblogin. This project may be found at:        *
# *   https://github.com/IdentityBank/Php_idblogin.                       *
# *                                                                       *
# *   Copyright (C) 2020 by Identity Bank. All Rights Reserved.           *
# *   https://www.identitybank.eu - You belong to you                     *
# *                                                                       *
# *   This program is free software: you can redistribute it and/or       *
# *   modify it under the terms of the GNU Affero General Public          *
# *   License as published by the Free Software Foundation, either        *
# *   version 3 of the License, or (at your option) any later version.    *
# *                                                                       *
# *   This program is distributed in the hope that it will be useful,     *
# *   but WITHOUT ANY WARRANTY; without even the implied warranty of      *
# *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the        *
# *   GNU Affero General Public License for more details.                 *
# *                                                                       *
# *   You should have received a copy of the GNU Affero General Public    *
# *   License along with this program. If not, see                        *
# *   https://www.gnu.org/licenses/.                                      *
# *                                                                       *
# * ********************************************************************* *

################################################################################
# Namespace                                                                    #
################################################################################

namespace app\controllers;

################################################################################
# Use(s)                                                                       #
################################################################################

use app\helpers\LoginConfig;
use app\models\IdbLoginForm;
use Exception;
use idbyii2\helpers\IdbSecurity;
use idbyii2\helpers\Localization;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

################################################################################
# Class(es)                                                                    #
################################################################################

/**
 * Class SiteController
 *
 * @package app\controllers
 */
class SiteController extends Controller
{

    /**
     * @param $action
     *
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;

        return parent::beforeAction($action);
    }

    /**
     * @param null $language
     * @param null $accountNumber
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex($language = null, $accountNumber = null)
    {
        $this->setLanguage($language);
        $model = new IdbLoginForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate(['userId', 'accountNumber'])) {
                $this->destinationLogin($model);
            } else {
                $this->delayRequest();
            }
        } elseif (!empty($accountNumber)) {
            $model->accountNumber = $accountNumber;
            if (!$model->validate(['accountNumber'])) {
                $model = new IdbLoginForm();
            }
        }

        $businessSignUp = LoginConfig::get()->createBusinessAccount();

        return $this->render(
            'login',
            [
                'model' => $model,
                'businessSignUp' => $businessSignUp
            ]
        );
    }

    /**
     * @param $language
     */
    private function setLanguage($language)
    {
        if (preg_match("/^[\w]{2}[-_][\w]{2}$/s", $language)) {
            $language = str_replace('_', '-', $language);
        } elseif (preg_match("/^[\w]{2}$/s", $language)) {
            $language = Localization::getLocale($language);
        }
        if (empty($language)) {
            $language = LoginConfig::get()->getLanguage();
        }
        if (empty($language)) {
            $language = 'en-GB';
        }
        Yii::$app->language = $language;
    }

    /**
     * @param $model
     *
     * @throws \yii\web\NotFoundHttpException
     */
    private function destinationLogin($model)
    {
        $destination = $model->getAccountDestination();
        $destinationUrls = LoginConfig::get()->getAccountDestinationUrls();
        if (is_array($destinationUrls) && !empty($destinationUrls[$destination])) {
            $url = $destinationUrls[$destination];
            $jwt = json_encode(array_intersect_key(Yii::$app->request->post(), ["IdbLoginForm" => null]));

            $accountDestinationPasswords = LoginConfig::get()->getAccountDestinationPasswords();
            $password = ((is_array($accountDestinationPasswords) && !empty($accountDestinationPasswords[$destination]))
                ? $accountDestinationPasswords[$destination] : '');
            $accountDestinationTokens = LoginConfig::get()->getAccountDestinationTokens();
            $token = ((is_array($accountDestinationTokens) && !empty($accountDestinationTokens[$destination]))
                ? $accountDestinationTokens[$destination] : '');
            $dateTimestamp = Localization::getDateTimeNumberString();
            $token .= $dateTimestamp;

            if (empty($password) || empty($token) || empty($url) || empty($jwt)) {
                throw new NotFoundHttpException();
            }
            $password = $token . $password;

            $idbSecurity = new IdbSecurity(Yii::$app->security);
            $jwt = base64_encode($idbSecurity->encryptByPassword($jwt, $password));
            $login = false;

            try {
                $postData = http_build_query(['idbjwt' => $jwt]);
                $httpHeader = ["X-Requested-With: XMLHttpRequest", "IDB-RequestVerificationToken: $dateTimestamp"];
                if (LoginConfig::get()->isDebug()) {
                    array_push($httpHeader, "Cookie: XDEBUG_SESSION=XDEBUG_IDB_LOGIN");
                }

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
                curl_setopt($ch, CURLOPT_POST, 1);
                $response = curl_exec($ch);
                if ($response === false) {
                    throw new Exception(curl_error($ch), curl_errno($ch));
                } else {
                    $response = json_decode($response, true);
                    if (!empty($response['status']) && $response['status'] === 'success') {
                        $login = true;
                    }
                }
                curl_close($ch);
            } catch (Exception $error) {
                $login = false;
            }

            if ($login) {
                $dateTimestamp = base64_encode($dateTimestamp);
                $jwt = substr_replace($jwt, $dateTimestamp, (strlen($jwt) / 2), 0);
                $jwt = urlencode($jwt);
                $url .= "?idbjwt=$jwt";
                if (!empty($response['idb'])) {
                    $url .= "&verify=".urlencode($response['idb']);
                }

                return $this->redirect($url)->send();
            } else {
                $this->delayRequest();
                $model->addFormError();
            }
        } else {
            throw new NotFoundHttpException();
        }
    }

    /**
     * Delay request with errors
     */
    private function delayRequest()
    {
        $randomNumber = intval(mt_rand(1, 9));
        sleep($randomNumber);
    }

    /**
     * @return string
     * @throws \yii\base\ExitException
     */
    public function actionError()
    {
        if (!LoginConfig::get()->isDebug()) {
            $this->redirect('https://www.identitybank.eu');
            Yii::$app->end();
            die();
        }
        $exception = Yii::$app->errorHandler->exception;

        if ($exception !== null) {
            $statusCode = Yii::$app->response->statusCode;
            $name = $exception->getName();
            $message = $exception->getMessage();

            return $this->render
            (
                'error',
                [
                    'exception' => $exception,
                    'statusCode' => $statusCode,
                    'name' => $name,
                    'message' => $message
                ]
            );
        }
    }
}

################################################################################
#                                End of file                                   #
################################################################################
