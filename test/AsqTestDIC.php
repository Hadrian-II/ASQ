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
use ilDBPdoMySQL;
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
        define('CLIENT_ID', 'default');
        define("IL_COMP_MODULE", "Modules");
        define("IL_COMP_SERVICE", "Services");
        define("IL_COMP_PLUGIN", "Plugins");
        define("IL_COMP_SLOTS", "Slots");

        $container = new AsqTestDIC();
        $GLOBALS['DIC'] = $container;

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

        $container['ilDb'] = function($c) {
            return new class() extends ilDBPdoMySQL {
                public function __construct() {}
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

        $container['upload'] = function($c) {
            return null;
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

    public function database()
    {
        return $this['ilDb'];
    }
}
