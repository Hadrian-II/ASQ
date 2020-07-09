<?php
/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace ILIAS\AssessmentQuestion\Test;

use ILIAS\DI\UIServices;
use Pimple\Container;
use ilCtrl;
use ilDBWrapperFactory;
use ilIniFile;
use ilLanguage;
use ilLoggerFactory;
use ilObjUser;

/**
 * Class AsqTestDIC
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class AsqTestDIC extends Container
{
    public static function init() : void
    {
        //TODO bad
        define('CLIENT_ID', 'default');

        $container = new AsqTestDIC();
        $GLOBALS['DIC'] = $container;

        $container['ilLoggerFactory'] = function ($c) {
            return ilLoggerFactory::getInstance();
        };

        $container['ilClientIniFile'] = function ($c) {
            //TODO bad
            $base_dir = '/var/www/ilias/';

            $ilIliasIniFile = new ilIniFile($base_dir . "ilias.ini.php");
            $ilIliasIniFile->read();

            // initialize constants
            define("ILIAS_DATA_DIR", $ilIliasIniFile->readVariable("clients", "datadir"));
            define("ILIAS_WEB_DIR", $ilIliasIniFile->readVariable("clients", "path"));
            define("ILIAS_ABSOLUTE_PATH", $ilIliasIniFile->readVariable('server', 'absolute_path'));

            // logging
            define("ILIAS_LOG_DIR", $ilIliasIniFile->readVariable("log", "path"));
            define("ILIAS_LOG_FILE", $ilIliasIniFile->readVariable("log", "file"));
            define("ILIAS_LOG_ENABLED", $ilIliasIniFile->readVariable("log", "enabled"));
            define("ILIAS_LOG_LEVEL", $ilIliasIniFile->readVariable("log", "level"));
            define("SLOW_REQUEST_TIME", $ilIliasIniFile->readVariable("log", "slow_request_time"));

            // read path + command for third party tools from ilias.ini
            define("PATH_TO_CONVERT", $ilIliasIniFile->readVariable("tools", "convert"));
            define("PATH_TO_FFMPEG", $ilIliasIniFile->readVariable("tools", "ffmpeg"));
            define("PATH_TO_ZIP", $ilIliasIniFile->readVariable("tools", "zip"));
            define("PATH_TO_MKISOFS", $ilIliasIniFile->readVariable("tools", "mkisofs"));
            define("PATH_TO_UNZIP", $ilIliasIniFile->readVariable("tools", "unzip"));
            define("PATH_TO_GHOSTSCRIPT", $ilIliasIniFile->readVariable("tools", "ghostscript"));
            define("PATH_TO_JAVA", $ilIliasIniFile->readVariable("tools", "java"));
            define("URL_TO_LATEX", $ilIliasIniFile->readVariable("tools", "latex"));
            define("PATH_TO_FOP", $ilIliasIniFile->readVariable("tools", "fop"));
            define("PATH_TO_LESSC", $ilIliasIniFile->readVariable("tools", "lessc"));
            define("PATH_TO_PHANTOMJS", $ilIliasIniFile->readVariable("tools", "phantomjs"));

            $ini_file = $base_dir . ILIAS_WEB_DIR . "/" . CLIENT_ID . "/client.ini.php";

            // get settings from ini file
            $ilClientIniFile = new ilIniFile($ini_file);
            $ilClientIniFile->read();

            // set constants
            define("SESSION_REMINDER_LEADTIME", 30);
            define("DEBUG", $ilClientIniFile->readVariable("system", "DEBUG"));
            define("DEVMODE", $ilClientIniFile->readVariable("system", "DEVMODE"));
            define("SHOWNOTICES", $ilClientIniFile->readVariable("system", "SHOWNOTICES"));
            define("DEBUGTOOLS", $ilClientIniFile->readVariable("system", "DEBUGTOOLS"));
            define("ROOT_FOLDER_ID", $ilClientIniFile->readVariable('system', 'ROOT_FOLDER_ID'));
            define("SYSTEM_FOLDER_ID", $ilClientIniFile->readVariable('system', 'SYSTEM_FOLDER_ID'));
            define("ROLE_FOLDER_ID", $ilClientIniFile->readVariable('system', 'ROLE_FOLDER_ID'));
            define("MAIL_SETTINGS_ID", $ilClientIniFile->readVariable('system', 'MAIL_SETTINGS_ID'));
            $error_handler = $ilClientIniFile->readVariable('system', 'ERROR_HANDLER');
            define("ERROR_HANDLER", $error_handler ? $error_handler : "PRETTY_PAGE");

            // this is for the online help installation, which sets OH_REF_ID to the
            // ref id of the online module
            define("OH_REF_ID", $ilClientIniFile->readVariable("system", "OH_REF_ID"));

            define("SYSTEM_MAIL_ADDRESS", $ilClientIniFile->readVariable('system', 'MAIL_SENT_ADDRESS')); // Change SS
            define("MAIL_REPLY_WARNING", $ilClientIniFile->readVariable('system', 'MAIL_REPLY_WARNING')); // Change SS

            define("CLIENT_DATA_DIR", ILIAS_DATA_DIR . "/" . CLIENT_ID);
            define("CLIENT_WEB_DIR", ILIAS_ABSOLUTE_PATH . "/" . ILIAS_WEB_DIR . "/" . CLIENT_ID);
            define("CLIENT_NAME", $ilClientIniFile->readVariable('client', 'name')); // Change SS

            $val = $ilClientIniFile->readVariable("db", "type");
            if ($val == "") {
                define("IL_DB_TYPE", "mysql");
            } else {
                define("IL_DB_TYPE", $val);
            }

            return $ilClientIniFile;
        };
        $force_init = $container['ilClientIniFile'];

        $container['ilDB'] = function($c) {
            $ilDB = ilDBWrapperFactory::getWrapper(IL_DB_TYPE);
            $ilDB->initFromIniFile();
            $ilDB->connect();
            return $ilDB;
        };

        $container['ilCtrl'] = function($c) {
            return new ilCtrl();
        };

        $container['ilUser'] = function($c) {
            return new ilObjUser();
        };

        $container['lng'] = function ($c) {
            return new ilLanguage('en');
        };


    }

    /**
     * Get interface to the Database.
     *
     * @return	\ilDBInterface
     */
    public function database()
    {
        return $this["ilDB"];
    }

    /**
     * Get the interface to the control structure.
     *
     * @return	\ilCtrl
     */
    public function ctrl()
    {
        return $this["ilCtrl"];
    }

    /**
     * Get the current user.
     *
     * @return	\ilObjUser
     */
    public function user()
    {
        return $this["ilUser"];
    }

    /**
     * Get interface to the i18n service.
     *
     * @return	\ilLanguage
     */
    public function language()
    {
        return $this["lng"];
    }

    /**
     * Get the interface to get services from UI framework.
     *
     * @return	UIServices
     */
    public function ui()
    {
        return new UIServices($this);
    }
}