<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Ordering\Form\Editor;

use ilTextAreaInputGUI;
use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Questions\Ordering\Editor\Data\OrderingTextEditorConfiguration;
use srag\asq\UserInterface\Web\Form\Factory\AbstractObjectFactory;

/**
 * Class OrderingTextEditorConfigurationFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class OrderingTextEditorConfigurationFactory extends AbstractObjectFactory
{
    const VAR_ORDERING_TEXT = "ote_text";

    /**
     * @param AbstractValueObject $value
     * @return array
     */
    public function getFormfields(?AbstractValueObject $value) : array
    {
        $fields = [];

        $text = new ilTextAreaInputGUI($this->language->txt('asq_ordering_text'), self::VAR_ORDERING_TEXT);
        $fields[self::VAR_ORDERING_TEXT] = $text;

        if ($value !== null) {
            $text->setValue($value->getText());
        }

        return $fields;
    }

    /**
     * @return OrderingTextEditorConfiguration
     */
    public function readObjectFromPost() : AbstractValueObject
    {
        return OrderingTextEditorConfiguration::createNew($this->readString(SELF::VAR_ORDERING_TEXT));
    }

    /**
     * @return OrderingTextEditorConfiguration
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return OrderingTextEditorConfiguration::createNew();
    }
}
