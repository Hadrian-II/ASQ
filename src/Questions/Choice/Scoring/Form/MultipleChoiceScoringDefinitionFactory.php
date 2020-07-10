<?php
declare(strict_types = 1);

namespace srag\asq\Questions\MultipleChoice\Form;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\Model\QuestionPlayConfiguration;
use srag\asq\UserInterface\Web\Fields\AsqTableInputFieldDefinition;
use srag\asq\UserInterface\Web\Form\AbstractAnswerOptionFactory;
use srag\asq\Questions\MultipleChoice\MultipleChoiceScoringDefinition;

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

    /**
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Form\IAnswerOptionFactory::getValues()
     */
    public function getValues(AbstractValueObject $definition) : array
    {
        return [
            self::VAR_MCSD_SELECTED => $definition->getPointsSelected(),
            self::VAR_MCSD_UNSELECTED => $definition->getPointsUnselected()
        ];
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Form\IAnswerOptionFactory::getTableColumns()
     */
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
        return MultipleChoiceScoringDefinition::create(
            floatval($values[self::VAR_MCSD_SELECTED]),
            floatval($values[self::VAR_MCSD_UNSELECTED])
        );
    }

    /**
     * @return MultipleChoiceScoringDefinition
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return MultipleChoiceScoringDefinition::create(null, null);
    }
}
