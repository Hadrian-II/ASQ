<?php
declare(strict_types = 1);

namespace srag\asq\Questions\ErrorText\Form\Scoring;

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
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Form\Factory\IObjectFactory::readObjectFromPost()
     */
    public function readObjectFromPost(array $postvalue) : AbstractValueObject
    {
        return ErrorTextScoringConfiguration::create($this->readFloat($postvalue[self::VAR_POINTS_WRONG]));
    }

    /**
     * @return ErrorTextScoringConfiguration
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return ErrorTextScoringConfiguration::create();
    }
}
