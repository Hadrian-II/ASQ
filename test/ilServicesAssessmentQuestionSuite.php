<?php
/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */


require_once(__DIR__ . "../../../../../../../../../../libs/composer/vendor/autoload.php");
require_once(__DIR__ . "../../vendor/autoload.php");
require_once('ASQTestDIC.php');

use PHPUnit\Framework\TestSuite;
use ILIAS\AssessmentQuestion\Test\AsqTestDIC;
use srag\asq\Application\Service\ASQDIC;

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
        'ErrorTextTest.php' => 'ILIAS\AssessmentQuestion\Test\ErrorTextTest',
        'EssayTest.php' => 'ILIAS\AssessmentQuestion\Test\EssayTest',
        'FileUploadTest.php' => 'ILIAS\AssessmentQuestion\Test\FileUploadTest',
        'ImageMapTest.php' => 'ILIAS\AssessmentQuestion\Test\ImageMapTest',
        'KprimTest.php' => 'ILIAS\AssessmentQuestion\Test\KprimTest',
        'MatchingTest.php' => 'ILIAS\AssessmentQuestion\Test\MatchingTest',
        'MultipleChoiceTest.php' => 'ILIAS\AssessmentQuestion\Test\MultiplechoiceTest',
        'NumericTest.php' => 'ILIAS\AssessmentQuestion\Test\NumericTest',
        'OrderingTest.php' => 'ILIAS\AssessmentQuestion\Test\OrderingTest'
    );

    public static function suite()
    {
        AsqTestDIC::init();
        ASQDIC::initiateASQ($GLOBALS['DIC']);

        $suite = new ilServicesAssessmentQuestionSuite();

        foreach (self::$testSuites as $classFile => $className) {
            require_once $classFile;
            $suite->addTestSuite($className);
        }

        return $suite;
    }
}
