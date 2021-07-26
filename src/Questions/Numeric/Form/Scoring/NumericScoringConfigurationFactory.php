<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Numeric\Form\Scoring;

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

    public function getFormfields(?AbstractValueObject $value) : array
    {
        $fields = [];

        $points = $this->factory->input()->field()->text($this->language->txt('asq_label_points'));

        $lower_bound = $this->factory->input()->field()->text($this->language->txt('asq_label_lower_bound'));

        $upper_bound = $this->factory->input()->field()->text($this->language->txt('asq_label_upper_bound'));

        if ($value !== null) {
            $points = $points->withValue(strval($value->getPoints()));
            $lower_bound = $lower_bound->withValue(strval($value->getLowerBound()));
            $upper_bound = $upper_bound->withValue(strval($value->getUpperBound()));
        }

        $fields[self::VAR_POINTS] = $points;
        $fields[self::VAR_LOWER_BOUND] = $lower_bound;
        $fields[self::VAR_UPPER_BOUND] = $upper_bound;

        return $fields;
    }

    /**
     * @param $postdata array
     * @return NumericScoringConfiguration
     */
    public function readObjectFromPost(array $postdata) : AbstractValueObject
    {
        return new NumericScoringConfiguration(
            $this->readFloat($postdata[self::VAR_POINTS]),
            $this->readFloat($postdata[self::VAR_LOWER_BOUND]),
            $this->readFloat($postdata[self::VAR_UPPER_BOUND])
        );
    }

    /**
     * @return NumericScoringConfiguration
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return new NumericScoringConfiguration();
    }
}
