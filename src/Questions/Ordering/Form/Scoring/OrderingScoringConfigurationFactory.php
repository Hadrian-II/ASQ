<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Ordering\Form\Scoring;

use Fluxlabs\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Questions\Ordering\Scoring\Data\OrderingScoringConfiguration;
use srag\asq\UserInterface\Web\Form\Factory\AbstractObjectFactory;

/**
 * Class OrderingScoringConfigurationFactory
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class OrderingScoringConfigurationFactory extends AbstractObjectFactory
{
    const VAR_POINTS = 'os_points';

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
     * @return OrderingScoringConfiguration
     */
    public function readObjectFromPost(array $postdata) : AbstractValueObject
    {
        return new OrderingScoringConfiguration($this->readFloat($postdata[self::VAR_POINTS]));
    }

    /**
     * @return OrderingScoringConfiguration
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return new OrderingScoringConfiguration();
    }
}
