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
# Use(s)                                                                       #
################################################################################

use app\helpers\LoginConfig;

################################################################################
# Load params                                                                  #
################################################################################

$params = require(__DIR__ . '/params.php');
$theme = LoginConfig::get()->getTheme();
$language = LoginConfig::get()->getLanguage();

################################################################################
# Web Config                                                                   #
################################################################################

$config =
    [
        'id' => 'IDB - Login',
        'name' => 'Identity Bank - Login',
        'version' => '1.0.1',
        'vendorPath' => $yii . '/vendor',
        'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
        'language' => $language,
        'sourceLanguage' => 'en-GB',
        'aliases' =>
            [
                '@idbyii2' => '/usr/local/share/p57b/php/idbyii2',
            ],
        'components' =>
            [
                'request' =>
                    [
                        'cookieValidationKey' => LoginConfig::get()->getYii2LoginCookieValidationKey(),
                    ],
                'assetManager' =>
                    [
                        'forceCopy' => false,
                        'appendTimestamp' => true,
                    ],
                'errorHandler' =>
                    [
                        'errorAction' => 'site/error',
                    ],
                'log' =>
                    [
                        'traceLevel' => YII_DEBUG ? 3 : 0,
                        'targets' =>
                            [
                                [
                                    'class' => 'yii\log\FileTarget',
                                    'levels' => ['error'],
                                ],
                                [
                                    'class' => 'yii\log\FileTarget',
                                    'logVars' => [],
                                    'categories' => ['login'],
                                    'levels' => ['info'],
                                    'logFile' => '@runtime/logs/info.log',
                                    'maxFileSize' => 1024 * 2,
                                    'maxLogFiles' => 10,
                                ],
                                [
                                    'class' => 'yii\log\FileTarget',
                                    'logVars' => [],
                                    'levels' => ['error', 'warning'],
                                    'logFile' => '/var/log/p57b/p57b.login-errors.log',
                                ],
                                [
                                    'class' => 'yii\log\FileTarget',
                                    'logVars' => [],
                                    'levels' => ['trace', 'info'],
                                    'logFile' => '/var/log/p57b/p57b.login-debug.log',
                                ],
                            ],
                    ],
                'urlManager' =>
                    [
                        'class' => 'yii\web\UrlManager',
                        'showScriptName' => false,
                        'enablePrettyUrl' => true,
                        'enableStrictParsing' => false,
                        'suffix' => '/',
                        'rules' =>
                            [
                                '/site/index' => '/site/index',
                                '/site/error' => '/site/error',
                                '/<language:[-\w]{2,5}>/<accountNumber:[-\w]{24}>' => '/site/index',
                                '/<accountNumber:[-\w]{24}>/<language:[-\w]{2,5}>' => '/site/index',
                                '/<language:[-\w]{2,5}>' => '/site/index',
                                '/<accountNumber:[-\w]{24}>' => '/site/index',
                                'defaultRoute' => '/site/index',
                            ],
                    ],
                'i18n' =>
                    [
                        'translations' =>
                            [
                                'login' =>
                                    [
                                        'class' => 'yii\i18n\PhpMessageSource',
                                        'sourceLanguage' => 'en-GB',
                                        'basePath' => '@app/messages',
                                    ],
                                'idbyii2' =>
                                    [
                                        'class' => 'yii\i18n\PhpMessageSource',
                                        'sourceLanguage' => 'en-GB',
                                        'basePath' => '@idbyii2/messages',
                                    ],
                                'idbexternal' => [
                                    'class' => 'yii\i18n\PhpMessageSource',
                                    'forceTranslation' => true,
                                    'sourceLanguage' => 'en-GB',
                                    'basePath' => '@idbyii2/messages',
                                ],
                            ],
                    ],
            ],
        'params' => $params,
    ];

if (!empty($theme)) {
    $config['components']['view'] =
        [
            'theme' =>
                [
                    'basePath' => '@app/themes/' . $theme,
                    'baseUrl' => '@web/themes/' . $theme,
                    'pathMap' =>
                        [
                            '@app/views' => '@app/themes/' . $theme . '/views',
                            '@app/modules' => '@app/themes/' . $theme . '/modules',
                        ],
                ]
        ];
}

if (YII_DEBUG) {
    $config['bootstrap'] = ['log'];
}

return $config;

################################################################################
#                                End of file                                   #
################################################################################
