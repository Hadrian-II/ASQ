<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Ordering\Form\Scoring;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Questions\Ordering\Scoring\Data\OrderingScoringConfiguration;
use srag\asq\UserInterface\Web\Form\Factory\AbstractObjectFactory;

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
     * @param AbstractValueObject $value
     * @return array
     */
    public function getFormfields(?AbstractValueObject $value) : array
    {
        $fields = [];

        $points = $this->factory->input()->field()->text($this->language->txt('asq_label_points'));


        if ($value !== null) {
            $points = $points->withValue(strval($value->getPoints()));
        }

        $fields[self::VAR_POINTS] = $points;

        return $fields;
    }

    /**
     * @param $postdata array
     * @return OrderingScoringConfigurationFactory
     */
    public function readObjectFromPost(array $postdata) : AbstractValueObject
    {
        return new OrderingScoringConfiguration($this->readFloat($postdata[self::VAR_POINTS]));
    }

    /**
     * @return OrderingScoringConfigurationFactory
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return new OrderingScoringConfiguration();
    }
}
