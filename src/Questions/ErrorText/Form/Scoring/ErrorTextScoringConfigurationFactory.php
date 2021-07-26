<?php
declare(strict_types = 1);

namespace srag\asq\Questions\ErrorText\Form\Scoring;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Questions\ErrorText\Scoring\Data\ErrorTextScoringConfiguration;
use srag\asq\UserInterface\Web\Form\Factory\AbstractObjectFactory;

/**
 * Class ErrorTextScoringConfigurationFactory
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class ErrorTextScoringConfigurationFactory extends AbstractObjectFactory
{
    const VAR_POINTS_WRONG = 'ets_points_wrong';

    /**
     * @param $value ?ErrorTextScoringConfiguration
     * @return array
     */
    public function getFormfields(?AbstractValueObject $value) : array
    {
        $fields = [];

        $points_wrong = $this->factory->input()->field()->text(
            $this->language->txt('asq_label_points_wrong'),
            $this->language->txt('asq_info_points_wrong')
        );

        if ($value !== null) {
            $points_wrong = $points_wrong->withValue(strval($value->getPointsWrong()));
        }

        $fields[self::VAR_POINTS_WRONG] = $points_wrong;

        return $fields;
    }

    /**
     * @return ErrorTextScoringConfiguration
     */
    public function readObjectFromPost(array $postvalue) : AbstractValueObject
    {
        return new ErrorTextScoringConfiguration($this->readFloat($postvalue[self::VAR_POINTS_WRONG]));
    }

    /**
     * @return ErrorTextScoringConfiguration
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return new ErrorTextScoringConfiguration();
    }
}
