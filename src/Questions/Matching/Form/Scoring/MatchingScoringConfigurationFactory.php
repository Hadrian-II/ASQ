<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Matching\Form\Scoring;

use ilNumberInputGUI;
use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Questions\Matching\Scoring\Data\MatchingScoringConfiguration;
use srag\asq\UserInterface\Web\Form\Factory\AbstractObjectFactory;

/**
 * Class MatchingScoringConfigurationFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class MatchingScoringConfigurationFactory extends AbstractObjectFactory
{
    const VAR_WRONG_DEDUCTION = 'ms_wrong_deduction';

    /**
     * @param AbstractValueObject $value
     * @return array
     */
    public function getFormfields(?AbstractValueObject $value) : array
    {
        $fields = [];

        $wrong_deduction = new ilNumberInputGUI(
            $this->language->txt('asq_label_wrong_deduction'),
            self::VAR_WRONG_DEDUCTION
        );
        $wrong_deduction->setSize(2);
        $fields[self::VAR_WRONG_DEDUCTION] = $wrong_deduction;

        if (!is_null($value)) {
            $wrong_deduction->setValue($value->getWrongDeduction());
        }

        return $fields;
    }

    /**
     * @return MatchingScoringConfiguration
     */
    public function readObjectFromPost() : AbstractValueObject
    {
        return MatchingScoringConfiguration::create($this->readFloat(self::VAR_WRONG_DEDUCTION));
    }

    /**
     * @return MatchingScoringConfiguration
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return MatchingScoringConfiguration::create();
    }
}
