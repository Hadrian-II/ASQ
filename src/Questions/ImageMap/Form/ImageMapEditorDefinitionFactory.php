<?php
declare(strict_types = 1);

namespace srag\asq\Questions\ImageMap\Form;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\Model\QuestionPlayConfiguration;
use srag\asq\UserInterface\Web\Fields\AsqTableInputFieldDefinition;
use srag\asq\UserInterface\Web\Form\AbstractAnswerOptionFactory;
use srag\asq\Questions\ImageMap\ImageMapEditorDefinition;

/**
 * Class ImageMapEditorDefinitionFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class ImageMapEditorDefinitionFactory extends AbstractAnswerOptionFactory
{
    const VAR_TOOLTIP = 'imedd_tooltip';
    const VAR_TYPE = 'imedd_type';
    const VAR_COORDINATES = 'imedd_coordinates';

    /**
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Form\IAnswerOptionFactory::getValues()
     */
    public function getValues(AbstractValueObject $definition): array
    {
        return [
            self::VAR_TOOLTIP => $definition->getTooltip(),
            self::VAR_TYPE => $definition->getType(),
            self::VAR_COORDINATES => $definition->getCoordinates()
        ];
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Form\IAnswerOptionFactory::getTableColumns()
     */
    public function getTableColumns(?QuestionPlayConfiguration $play): array
    {
        $fields = [];

        $fields[] = new AsqTableInputFieldDefinition(
            $this->language->txt('asq_label_tooltip'),
            AsqTableInputFieldDefinition::TYPE_TEXT,
            self::VAR_TOOLTIP
            );

        $fields[] = new AsqTableInputFieldDefinition(
            $this->language->txt('asq_label_type'),
            AsqTableInputFieldDefinition::TYPE_DROPDOWN,
            self::VAR_TYPE,
            [
                ImageMapEditorDefinition::TYPE_RECTANGLE => $this->language->txt('asq_option_rectangle'),
                ImageMapEditorDefinition::TYPE_CIRCLE => $this->language->txt('asq_option_circle'),
                ImageMapEditorDefinition::TYPE_POLYGON => $this->language->txt('asq_option_polygon')
            ]);

        $fields[] = new AsqTableInputFieldDefinition(
            $this->language->txt('asq_label_coordinates'),
            AsqTableInputFieldDefinition::TYPE_LABEL,
            self::VAR_COORDINATES
            );

        $fields[] = new AsqTableInputFieldDefinition(
            '',
            AsqTableInputFieldDefinition::TYPE_HIDDEN,
            self::VAR_COORDINATES
            );

        $fields[] = new AsqTableInputFieldDefinition(
            '',
            AsqTableInputFieldDefinition::TYPE_BUTTON,
            'btn-coordinates',
            [
                'css' => 'js_select_coordinates',
                'title' => $this->language->txt('asq_label_select_coordinates')
            ]);

        return $fields;
    }

    /**
     * @return ImageMapEditorDefinition
     */
    public function readObjectFromValues(array $values): AbstractValueObject
    {
        return ImageMapEditorDefinition::create(
            $values[self::VAR_TOOLTIP],
            intval($values[self::VAR_TYPE]),
            $values[self::VAR_COORDINATES]);
    }

    /**
     * @return ImageMapEditorDefinition
     */
    public function getDefaultValue(): AbstractValueObject
    {
        return ImageMapEditorDefinition::create(null, null, null);
    }
}