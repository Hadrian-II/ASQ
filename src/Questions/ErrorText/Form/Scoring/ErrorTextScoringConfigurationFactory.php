<?php
declare(strict_types = 1);

namespace srag\asq\Questions\ErrorText\Form\Scoring;

use ilNumberInputGUI;
use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Questions\ErrorText\Scoring\Data\ErrorTextScoringConfiguration;
use srag\asq\UserInterface\Web\Form\Factory\AbstractObjectFactory;

/**
 * Class ErrorTextScoringConfigurationFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class ErrorTextScoringConfigurationFactory extends AbstractObjectFactory
{
    const VAR_POINTS_WRONG = 'ets_points_wrong';

    /**
     * @param $value ErrorTextScoringConfiguration
     * @return array
     */
    public function getFormfields(?AbstractValueObject $value) : array
    {
        $fields = [];

        $points_wrong = new ilNumberInputGUI($this->language->txt('asq_label_points_wrong'), self::VAR_POINTS_WRONG);
        $points_wrong->setSize(6);
        $points_wrong->setRequired(true);
        $points_wrong->setMaxValue(0);
        $points_wrong->setInfo($this->language->txt('asq_info_points_wrong'));
        $fields[self::VAR_POINTS_WRONG] = $points_wrong;

        if ($value !== null) {
            $points_wrong->setValue($value->getPointsWrong());
        }

        return $fields;
    }

    /**
     * @return ErrorTextScoringConfiguration
     */
    public function readObjectFromPost() : AbstractValueObject
    {
        return ErrorTextScoringConfiguration::create($this->readFloat(self::VAR_POINTS_WRONG));
    }

    /**
     * @return ErrorTextScoringConfiguration
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return ErrorTextScoringConfiguration::create();
    }
}
