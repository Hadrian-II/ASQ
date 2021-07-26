<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Choice\Form\Scoring;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\Model\Configuration\QuestionPlayConfiguration;
use srag\asq\Questions\Choice\Scoring\Data\MultipleChoiceScoringDefinition;
use srag\asq\UserInterface\Web\Fields\AsqTableInput\AsqTableInputFieldDefinition;
use srag\asq\UserInterface\Web\Form\Factory\AbstractAnswerOptionFactory;

/**
 * Class MultipleChoiceScoringDefinitionFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class MultipleChoiceScoringDefinitionFactory extends AbstractAnswerOptionFactory
{
    const VAR_MCSD_SELECTED = 'mcsd_selected';
    const VAR_MCSD_UNSELECTED = 'mcsd_unselected';

    public function getValues(AbstractValueObject $definition) : array
    {
        return [
            self::VAR_MCSD_SELECTED => $definition->getPointsSelected(),
            self::VAR_MCSD_UNSELECTED => $definition->getPointsUnselected()
        ];
    }

    public function getTableColumns(?QuestionPlayConfiguration $play) : array
    {
        $fields = [];

        $fields[self::VAR_MCSD_SELECTED] = new AsqTableInputFieldDefinition(
            $this->language->txt('asq_label_checked'),
            AsqTableInputFieldDefinition::TYPE_NUMBER,
            self::VAR_MCSD_SELECTED
        );

        $fields[self::VAR_MCSD_UNSELECTED] = new AsqTableInputFieldDefinition(
            $this->language->txt('asq_label_unchecked'),
            AsqTableInputFieldDefinition::TYPE_NUMBER,
            self::VAR_MCSD_UNSELECTED
        );

        return $fields;
    }

    /**
     * @return MultipleChoiceScoringDefinition
     */
    public function readObjectFromValues(array $values) : AbstractValueObject
    {
        return new MultipleChoiceScoringDefinition(
            $this->readFloat($values[self::VAR_MCSD_SELECTED]),
            $this->readFloat($values[self::VAR_MCSD_UNSELECTED])
        );
    }

    /**
     * @return MultipleChoiceScoringDefinition
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return new MultipleChoiceScoringDefinition();
    }
}
