<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Choice\Form\Editor\ImageMap;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\Model\Configuration\QuestionPlayConfiguration;
use srag\asq\Questions\Choice\Editor\ImageMap\Data\ImageMapEditorDefinition;
use srag\asq\UserInterface\Web\Fields\AsqTableInput\AsqTableInputFieldDefinition;
use srag\asq\UserInterface\Web\Form\Factory\AbstractAnswerOptionFactory;

/**
 * Class ImageMapEditorDefinitionFactory
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class ImageMapEditorDefinitionFactory extends AbstractAnswerOptionFactory
{
    const VAR_TOOLTIP = 'imedd_tooltip';
    const VAR_TYPE = 'imedd_type';
    const VAR_COORDINATES = 'imedd_coordinates';

    public function getValues(AbstractValueObject $definition) : array
    {
        return [
            self::VAR_TOOLTIP => $definition->getTooltip(),
            self::VAR_TYPE => $definition->getType(),
            self::VAR_COORDINATES => $definition->getCoordinates()
        ];
    }

    public function getTableColumns(?QuestionPlayConfiguration $play) : array
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
            ]
        );

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
            ]
        );

        return $fields;
    }

    /**
     * @return ImageMapEditorDefinition
     */
    public function readObjectFromValues(array $values) : AbstractValueObject
    {
        return new ImageMapEditorDefinition(
            $values[self::VAR_TOOLTIP],
            intval($values[self::VAR_TYPE]),
            $values[self::VAR_COORDINATES]
        );
    }

    /**
     * @return ImageMapEditorDefinition
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return new ImageMapEditorDefinition();
    }
}
