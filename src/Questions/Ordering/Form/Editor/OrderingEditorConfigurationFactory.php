<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Ordering\Form\Editor;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Questions\Ordering\Editor\Data\OrderingEditorConfiguration;
use srag\asq\UserInterface\Web\Form\Factory\AbstractObjectFactory;

/**
 * Class OrderingEditorConfigurationFactory
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class OrderingEditorConfigurationFactory extends AbstractObjectFactory
{
    const VAR_VERTICAL = "oe_vertical";

    const VERTICAL = "vertical";
    const HORICONTAL = "horicontal";

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
            if (! is_null($value->isVertical())) {
                $is_vertical = $is_vertical->withValue($value->isVertical() ? self::VERTICAL : self::HORICONTAL);
            }
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
        $raw_order = $this->readString($postdata[self::VAR_VERTICAL]);

        if ($raw_order === self::VERTICAL || $raw_order === self::HORICONTAL) {
            $order = $raw_order === self::VERTICAL;
        }
        else {
            $order = null;
        }

        return new OrderingEditorConfiguration($order);
    }

    /**
     * @return OrderingEditorConfiguration
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return new OrderingEditorConfiguration();
    }
}
