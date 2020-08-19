<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Ordering\Form\Editor;

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

        $text = $this->factory->input()->field()->text($this->language->txt('asq_ordering_text'));

        if ($value !== null) {
            $text = $text->withValue($value->getText() ?? '');
        }

        $fields[self::VAR_ORDERING_TEXT] = $text;

        return $fields;
    }

    /**
     * @param $postdata array
     * @return OrderingTextEditorConfiguration
     */
    public function readObjectFromPost(array $postdata) : AbstractValueObject
    {
        return OrderingTextEditorConfiguration::createNew($this->readString($postdata[SELF::VAR_ORDERING_TEXT]));
    }

    /**
     * @return OrderingTextEditorConfiguration
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return OrderingTextEditorConfiguration::createNew();
    }
}
