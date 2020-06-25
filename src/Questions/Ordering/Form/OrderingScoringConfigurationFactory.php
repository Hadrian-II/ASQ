<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Ordering\Form;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\UserInterface\Web\Form\AbstractObjectFactory;
use ilNumberInputGUI;
use srag\asq\Questions\Ordering\OrderingScoringConfiguration;

/**
 * Class OrderingScoringConfigurationFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class OrderingScoringConfigurationFactory extends AbstractObjectFactory
{
    const VAR_POINTS = 'os_points';

    /**
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Form\IObjectFactory::getFormfields()
     */
    public function getFormfields(?AbstractValueObject $value) : array
    {
        $fields = [];

        $points = new ilNumberInputGUI($this->language->txt('asq_label_points'), self::VAR_POINTS);
        $points->setRequired(true);
        $points->setSize(2);
        $fields[self::VAR_POINTS] = $points;

        if ($value !== null) {
            $points->setValue($value->getPoints());
        }

        return $fields;
    }

    /**
     * @return OrderingScoringConfigurationFactory
     */
    public function readObjectFromPost(): AbstractValueObject
    {
        return OrderingScoringConfiguration::create($this->readFloat(self::VAR_POINTS));
    }

    /**
     * @return OrderingScoringConfigurationFactory
     */
    public function getDefaultValue(): AbstractValueObject
    {
        return OrderingScoringConfiguration::create();
    }
}