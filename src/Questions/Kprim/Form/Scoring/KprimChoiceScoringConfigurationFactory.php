<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Kprim\Form\Scoring;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Questions\Kprim\Scoring\Data\KprimChoiceScoringConfiguration;
use srag\asq\UserInterface\Web\Form\Factory\AbstractObjectFactory;

/**
 * Class KprimScoringConfigurationFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class KprimChoiceScoringConfigurationFactory extends AbstractObjectFactory
{
    const VAR_POINTS = 'kcs_points';
    const VAR_HALF_POINTS = 'kcs_half_points_at';

    const HALFPOINTS_AT = 3;

    /**
     * @param AbstractValueObject $value
     * @return array
     */
    public function getFormfields(?AbstractValueObject $value) : array
    {
        $fields = [];

        $points = $this->factory->input()->field()->text($this->language->txt('asq_label_points'));

        $half_points_at = $this->factory->input()->field()->checkbox(
            $this->language->txt('asq_label_half_points'),
            $this->language->txt('asq_description_half_points'));

        if ($value !== null) {
            $points = $points->withValue(strval($value->getPoints()));
            $half_points_at = $half_points_at->withValue($value->getHalfPointsAt() === self::HALFPOINTS_AT);
        }

        $fields[self::VAR_POINTS] = $points;
        $fields[self::VAR_HALF_POINTS] = $half_points_at;

        return $fields;
    }

    /**
     * @param $postdata array
     * @return KprimChoiceScoringConfiguration
     */
    public function readObjectFromPost(array $postdata) : AbstractValueObject
    {
        return KprimChoiceScoringConfiguration::create(
            $this->readFloat($postdata[self::VAR_POINTS]),
            $postdata[self::VAR_HALF_POINTS] ? self::HALFPOINTS_AT : null
        );
    }

    /**
     * @return KprimChoiceScoringConfiguration
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return KprimChoiceScoringConfiguration::create();
    }
}
