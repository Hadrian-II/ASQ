<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Ordering\Form\Editor;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Questions\Ordering\Editor\Data\OrderingEditorConfiguration;
use srag\asq\UserInterface\Web\Form\Factory\AbstractObjectFactory;

/**
 * Class OrderingEditorConfigurationFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class OrderingEditorConfigurationFactory extends AbstractObjectFactory
{
    const VAR_VERTICAL = "oe_vertical";

    const VERTICAL = "vertical";
    const HORICONTAL = "horicontal";

    /**
     * @param AbstractValueObject $value
     * @return array
     */
    public function getFormfields(?AbstractValueObject $value) : array
    {
        $fields = [];

        $is_vertical = $this->factory->input()->field()->select(
            $this->language->txt('asq_label_is_vertical'),
            [
                self::VERTICAL => $this->language->txt('asq_label_vertical'),
                self::HORICONTAL => $this->language->txt('asq_label_horicontal')
            ]
        );

        if ($value !== null) {
            $is_vertical = $is_vertical->withValue($value->isVertical() ? self::VERTICAL : self::HORICONTAL);
        } else {
            $is_vertical = $is_vertical->withValue(self::VERTICAL);
        }

        $fields[self::VAR_VERTICAL] = $is_vertical;

        return $fields;
    }

    /**
     * @param $postdata array
     * @return OrderingEditorConfiguration
     */
    public function readObjectFromPost(array $postdata) : AbstractValueObject
    {
        return OrderingEditorConfiguration::create($this->readString($postdata[self::VAR_VERTICAL]) === self::VERTICAL);
    }

    /**
     * @return OrderingEditorConfiguration
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return OrderingEditorConfiguration::create();
    }
}
