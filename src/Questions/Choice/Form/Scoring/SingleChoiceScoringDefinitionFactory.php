<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Choice\Form\Scoring;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\Model\Configuration\QuestionPlayConfiguration;
use srag\asq\Questions\Choice\Scoring\Data\MultipleChoiceScoringDefinition;
use srag\asq\UserInterface\Web\Fields\AsqTableInput\AsqTableInputFieldDefinition;
use srag\asq\UserInterface\Web\Form\Factory\AbstractAnswerOptionFactory;

/**
 * Class SingleChoiceScoringDefinitionFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class SingleChoiceScoringDefinitionFactory extends AbstractAnswerOptionFactory
{
    const VAR_MCSD_SELECTED = 'mcsd_selected';

    const ZERO_BY_DEFAULT = 0;

    /**
     * @param AbstractValueObject $definition
     * @return array
     */
    public function getValues(AbstractValueObject $definition) : array
    {
        return [
            self::VAR_MCSD_SELECTED => $definition->getPointsSelected()
        ];
    }

    /**
     * @param QuestionPlayConfiguration $play
     * @return array
     */
    public function getTableColumns(?QuestionPlayConfiguration $play) : array
    {
        $fields = [];

        $fields[self::VAR_MCSD_SELECTED] = new AsqTableInputFieldDefinition(
            $this->language->txt('asq_label_points'),
            AsqTableInputFieldDefinition::TYPE_NUMBER,
            self::VAR_MCSD_SELECTED
        );

        return $fields;
    }

    /**
     * @return MultipleChoiceScoringDefinition
     */
    public function readObjectFromValues(array $values) : AbstractValueObject
    {
        return new MultipleChoiceScoringDefinition(
            floatval($values[self::VAR_MCSD_SELECTED]),
            self::ZERO_BY_DEFAULT
        );
    }

    /**
     * @return MultipleChoiceScoringDefinition
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return new MultipleChoiceScoringDefinition(null, self::ZERO_BY_DEFAULT);
    }
}
