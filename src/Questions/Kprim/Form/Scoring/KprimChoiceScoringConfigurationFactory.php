<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Kprim\Form\Scoring;

use ilCheckboxInputGUI;
use ilNumberInputGUI;
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

        $points = new ilNumberInputGUI($this->language->txt('asq_label_points'), self::VAR_POINTS);
        $points->setRequired(true);
        $points->setSize(2);
        $fields[self::VAR_POINTS] = $points;

        $half_points_at = new ilCheckboxInputGUI($this->language->txt('asq_label_half_points'), self::VAR_HALF_POINTS);
        $half_points_at->setInfo($this->language->txt('asq_description_half_points'));
        $half_points_at->setValue(self::HALFPOINTS_AT);
        $fields[self::VAR_HALF_POINTS] = $half_points_at;

        if ($value !== null) {
            $points->setValue($value->getPoints());
            $half_points_at->setChecked($value->getHalfPointsAt() === self::HALFPOINTS_AT);
        }

        return $fields;
    }

    /**
     * @return KprimChoiceScoringConfiguration
     */
    public function readObjectFromPost() : AbstractValueObject
    {
        return KprimChoiceScoringConfiguration::create(
            $this->readFloat(self::VAR_POINTS),
            $this->readInt(self::VAR_HALF_POINTS)
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
