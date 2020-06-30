<?php
/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */

use PHPUnit\Framework\TestSuite;

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
        'Services/AssessmentQuestion/test/ClozeQuestionTest.php' => 'ILIAS\AssessmentQuestion\Questions\Cloze\ClozeQuestionTest'
    );
    
    public static function suite()
    {
        if (defined('ILIAS_PHPUNIT_CONTEXT')) {
            include_once("./Services/PHPUnit/classes/class.ilUnitUtil.php");
            ilUnitUtil::performInitialisation();
        } else {
            chdir(dirname(__FILE__));
            chdir('../../../');
        }
        
        $suite = new ilServicesAssessmentQuestionSuite();
        
        foreach (self::$testSuites as $classFile => $className) {
            require_once $classFile;
            $suite->addTestSuite($className);
        }
        
        return $suite;
    }
}
