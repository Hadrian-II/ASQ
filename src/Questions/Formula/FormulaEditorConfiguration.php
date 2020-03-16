<?php

namespace ILIAS\AssessmentQuestion\Questions\Formula;

use ILIAS\AssessmentQuestion\DomainModel\AbstractConfiguration;
use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class KprimChoiceEditorConfiguration
 *
 * @package ILIAS\AssessmentQuestion\Authoring\DomainModel\Question\Answer\Option;
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 * @author  Björn Heyser <bh@bjoernheyser.de>
 * @author  Martin Studer <ms@studer-raimann.ch>
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 */
class FormulaEditorConfiguration extends AbstractConfiguration {

    public static function create() : FormulaEditorConfiguration {
        return new FormulaEditorConfiguration();
    }
    
    // Empty class as Formulaquestion has no clear divide between editor and display
    // So all data is stored in Scoring
    
    public function equals(AbstractValueObject $other): bool
    {
        return get_class($this) === get_class($other);
    }
}