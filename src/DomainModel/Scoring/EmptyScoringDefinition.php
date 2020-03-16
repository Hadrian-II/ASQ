<?php

namespace ILIAS\AssessmentQuestion\DomainModel\Scoring;

use ILIAS\AssessmentQuestion\DomainModel\QuestionPlayConfiguration;
use ILIAS\AssessmentQuestion\DomainModel\Answer\Option\AnswerDefinition;

/**
 * Class EmptyScoringDefinition
 *
 * @package ILIAS\AssessmentQuestion\Authoring\DomainModel\Question\Answer\Option;
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 * @author  Björn Heyser <bh@bjoernheyser.de>
 * @author  Martin Studer <ms@studer-raimann.ch>
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 */
class EmptyScoringDefinition extends AnswerDefinition {
    
    public static function getFields(QuestionPlayConfiguration $play): array {
        return [];
    }
    
    public static function getValueFromPost($index) {
        return new EmptyScoringDefinition();
    }
    
    public function getValues(): array {
        return [];
    }
    
    
    public static function deserialize($data) {
        return new EmptyScoringDefinition();
    }
}