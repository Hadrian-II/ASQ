<?php
/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */


require_once("../../../../../../../../../libs/composer/vendor/autoload.php");
require_once("../../vendor/composer/autoload_static.php");
require_once('ASQTestDIC.php');

use PHPUnit\Framework\TestSuite;
use ILIAS\AssessmentQuestion\Test\AsqTestDIC;

/**
 * Class ilServicesAssessmentQuestionSuite
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class ilServicesAssessmentQuestionSuite extends TestSuite
{
    /**
     * @var array
     */
    protected static $testSuites = array(
        'ClozeQuestionTest.php' => 'ILIAS\AssessmentQuestion\Test\ClozeQuestionTest'
    );

    public static function suite()
    {
        AsqTestDIC::init();

        $suite = new ilServicesAssessmentQuestionSuite();

        foreach (self::$testSuites as $classFile => $className) {
            require_once $classFile;
            $suite->addTestSuite($className);
        }

        return $suite;
    }
}
