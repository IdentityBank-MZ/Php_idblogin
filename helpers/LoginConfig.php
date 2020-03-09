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

namespace app\helpers;

################################################################################
# Use(s)                                                                       #
################################################################################

use idbyii2\helpers\IdbYii2Config;
use idbyii2\helpers\Localization;

################################################################################
# IDB Login Config                                                                 #
################################################################################

const loginConfigFile = '/etc/p57b/idblogin.jsc';

################################################################################
# Class(es)                                                                    #
################################################################################

class LoginConfig extends IdbYii2Config
{

    private static $instance;

    final protected function __construct()
    {
        parent::__construct();
        $this->mergeJscFile(loginConfigFile);
    }

    public static function get()
    {
        if (!isset(self::$instance) || !self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    function isDebug()
    {
        return $this->getValue(null, 'debug', false);
    }

    function getYii2LoginCookieValidationKey()
    {
        return $this->getValue('"Yii2"."idblogin"', 'cookieValidationKey', 'IDB');
    }

    function getLanguage()
    {
        if ($this->useBrowserLocalization()) {
            $language = Localization::getBrowserLocalization($this->getDefaultLocalizationLanguage());
        } else {
            $language = $this->getWebLanguage();
        }

        return $language;
    }

    function useBrowserLocalization()
    {
        return $this->getValue('Localization', 'browser');
    }

    function getDefaultLocalizationLanguage()
    {
        return 'en-GB';
    }

    function getWebLanguage()
    {
        $language = $this->getValue('Localization', 'webLanguage', $this->getDefaultLocalizationLanguage());
        if (empty($language)) {
            $language = $this->getDefaultLocalizationLanguage();
        }

        return $language;
    }

    function getTheme()
    {
        $theme = $this->getValue(null, 'theme', "idb");
        if (empty($theme)) {
            $theme = "idb";
        }

        return $theme;
    }

    function createBusinessAccount()
    {
        return $this->getValue('"Yii2"."idblogin"', 'businessSignUp');
    }
}

################################################################################
#                                End of file                                   #
################################################################################
