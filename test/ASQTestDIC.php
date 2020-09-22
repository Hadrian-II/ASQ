<?php
/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace ILIAS\AssessmentQuestion\Test;

use ILIAS\DI\UIServices;
use ilCtrl;
use ilIniFile;
use ilLanguage;
use ilObjUser;
use ilBenchmark;
use ilSetting;
use ilGlobalPageTemplate;
use ILIAS\GlobalScreen\Services;
use ILIAS\DI\HTTPServices;
use ilStyleDefinition;
use ilLogger;
use ILIAS\UI\Implementation\Factory as UIFactory;
use ILIAS\UI\Implementation\Render\ilTemplateWrapperFactory;
use ILIAS\UI\Implementation\Render\ilJavaScriptBinding;
use ILIAS\Refinery\Factory as Refinery;

/**
 * Class AsqTestDIC
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class AsqTestDIC extends \ILIAS\DI\Container
{
    public static function init() : void
    {
        //TODO bad
        define('CLIENT_ID', 'default');
        define("IL_COMP_MODULE", "Modules");
        define("IL_COMP_SERVICE", "Services");
        define("IL_COMP_PLUGIN", "Plugins");
        define("IL_COMP_SLOTS", "Slots");

        $container = new AsqTestDIC();
        $GLOBALS['DIC'] = $container;

//         $container['ilLoggerFactory'] = function ($c) {
//             return ilLoggerFactory::getInstance();
//         };

        $container['ilClientIniFile'] = function ($c) {
            //TODO bad
            $base_dir = '/var/www/ilias/';

            $ilIliasIniFile = new ilIniFile($base_dir . "ilias.ini.php");
            $ilIliasIniFile->read();
            $c['ilIliasIniFile'] = $ilIliasIniFile;

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

        $container['ilCtrl'] = function ($c) {
            return new ilCtrl();
        };

        $container['ilUser'] = function ($c) {
            return new class() {
                public $prefs = [
                    'language' => 'en',
                    'style' => 'asdf'
                ];

                public function getId()
                {
                    return 6;
                }
            };
        };

        $container['lng'] = function ($c) {
            return new class() extends ilLanguage {
                public function __construct() {}

                public function txt($a_topic, $a_default_lang_fallback_mod = "")
                {
                    return '$TRANSLATED_TEXT$';
                }

                public function loadLanguageModule($a_module) {}
            };
        };

        $container['ilias'] = function ($c) {
            return new class() {
                public $account;
                public $ini;

                public function __construct()
                {
                    $this->account = new class() {
                        public $id = 6;
                        public $fullname = 'Testa Testy';
                    };

                    $this->ini = new class() {
                        public function readVariable()
                        {
                            return '';
                        }
                    };
                }
            };
        };

        $container['ilLog'] = function ($c) {
            return new class() extends ilLogger
            {
                public function __construct() {

                }

                public function lang() {
                    return null;
                }
            };
        };

        $container['ilBench'] = function ($c) {
            return new ilBenchmark();
        };

        $container['ilErr'] = function ($c) {
            return null;
        };

        $container['ilAppEventHandler'] = function ($c) {
            return null;
        };

        $container['objDefinition'] = function ($c) {
            return null;
        };

        $container['ilSetting'] = function ($c) {
            return new class() extends ilSetting
            {
                public function __construct() {}
            };
        };

        $container['ilPluginAdmin'] = function ($c) {
            return new class() {
                public function getActivePluginsForSlot()
                {
                    return [];
                }
            };
        };

        $container['http'] = function ($c) {
            return new class() extends HTTPServices {
                public function __construct() {}
            };
        };

        $container['tpl'] = function ($c) {
            return new ilGlobalPageTemplate(
                new class() extends Services {
                    public function __construct()
                    {
                    }
                },
                new UIServices($c),
                $c['http']
            );
        };

        $container['ui.template_factory'] = function ($c) {
            return new ilTemplateWrapperFactory($c['tpl']);
        };

        $container['ui.javascript_binding'] = function ($c) {
            return new ilJavaScriptBinding($c['tpl']);
        };

        $container['refinery'] = function ($c) {
            return new Refinery(new \ILIAS\Data\Factory(), $c['lng']);
        };

        $container['styleDefinition'] = function ($c) {
            return new ilStyleDefinition();
        };

        $container['ui.factory'] = function ($c) {
            return new class() extends UIFactory {
                public function __construct() {}
            };
        };
    }

    public function repositoryTree()
    {
        return null;
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

    /**
     * @return \ilIniFile
     */
    public function iliasIni()
    {
        return $this['ilIliasIniFile'];
    }

    public function clientIni()
    {
        return $this['ilClientIniFile'];
    }

    public function logger()
    {
        return $this['ilLog'];
    }

    /**
     * Get the interface to the settings
     *
     * @return \ilSetting
     */
    public function settings()
    {
        return $this["ilSetting"];
    }

    public function isDependencyAvailable($name)
    {
        false;
    }
}
