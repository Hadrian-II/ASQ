<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Ordering\Form;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\UserInterface\Web\Form\AbstractObjectFactory;
use srag\asq\Questions\Ordering\OrderingEditorConfiguration;
use ilSelectInputGUI;

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
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Form\IObjectFactory::getFormfields()
     */
    public function getFormfields(?AbstractValueObject $value) : array
    {
        $fields = [];

        $is_vertical = new ilSelectInputGUI($this->language->txt('asq_label_is_vertical'), self::VAR_VERTICAL);
        $is_vertical->setOptions([
            self::VERTICAL => $this->language->txt('asq_label_vertical'),
            self::HORICONTAL => $this->language->txt('asq_label_horicontal')
        ]);
        $fields[self::VAR_VERTICAL] = $is_vertical;

        if ($value !== null) {
            $is_vertical->setValue($value->isVertical() ? self::VERTICAL : self::HORICONTAL);
        } else {
            $is_vertical->setValue(self::VERTICAL);
        }

        return $fields;
    }

    /**
     * @return OrderingEditorConfiguration
     */
    public function readObjectFromPost() : AbstractValueObject
    {
        return OrderingEditorConfiguration::create($this->readString(self::VAR_VERTICAL) === self::VERTICAL);
    }

    /**
     * @return OrderingEditorConfiguration
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return OrderingEditorConfiguration::create();
    }
}