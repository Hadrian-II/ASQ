<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Numeric\Form\Scoring;

use ilFormSectionHeaderGUI;
use ilNumberInputGUI;
use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Questions\Numeric\Scoring\Data\NumericScoringConfiguration;
use srag\asq\UserInterface\Web\Form\Factory\AbstractObjectFactory;

/**
 * Class NumericScoringConfigurationFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class NumericScoringConfigurationFactory extends AbstractObjectFactory
{
    const VAR_POINTS = 'ns_points';
    const VAR_LOWER_BOUND = 'ns_lower_bound';
    const VAR_UPPER_BOUND = 'ns_upper_bound';

    /**
     * @param AbstractValueObject $value
     * @return array
     */
    public function getFormfields(?AbstractValueObject $value) : array
    {
        $fields = [];

        $points = new ilNumberInputGUI($this->language->txt('asq_label_points'), self::VAR_POINTS);
        $points->setRequired(true);
        $points->setSize(2);
        $fields[self::VAR_POINTS] = $points;

        $spacer = new ilFormSectionHeaderGUI();
        $spacer->setTitle($this->language->txt('asq_range'));
        $fields[] = $spacer;

        $lower_bound = new ilNumberInputGUI($this->language->txt('asq_label_lower_bound'), self::VAR_LOWER_BOUND);
        $lower_bound->setRequired(true);
        $lower_bound->allowDecimals(true);
        $lower_bound->setSize(6);
        $fields[self::VAR_LOWER_BOUND] = $lower_bound;

        $upper_bound = new ilNumberInputGUI($this->language->txt('asq_label_upper_bound'), self::VAR_UPPER_BOUND);
        $upper_bound->setRequired(true);
        $upper_bound->allowDecimals(true);
        $upper_bound->setSize(6);
        $fields[self::VAR_UPPER_BOUND] = $upper_bound;

        if ($value !== null) {
            $points->setValue($value->getPoints());
            $lower_bound->setValue($value->getLowerBound());
            $upper_bound->setValue($value->getUpperBound());
        }

        return $fields;
    }

    /**
     * @return NumericScoringConfiguration
     */
    public function readObjectFromPost() : AbstractValueObject
    {
        return NumericScoringConfiguration::create(
            $this->readFloat(self::VAR_POINTS),
            $this->readFloat(self::VAR_LOWER_BOUND),
            $this->readFloat(self::VAR_UPPER_BOUND)
        );
    }

    /**
     * @return NumericScoringConfiguration
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return NumericScoringConfiguration::create();
    }
}
