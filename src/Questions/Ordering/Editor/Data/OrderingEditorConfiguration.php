<?php
declare(strict_types=1);

namespace srag\asq\Questions\Ordering\Editor\Data;

use srag\asq\Domain\Model\Configuration\AbstractConfiguration;

/**
 * Class OrderingEditorConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class OrderingEditorConfiguration extends AbstractConfiguration
{
    /**
     * @var ?bool
     */
    protected $vertical;

    /**
     * @param bool $vertical
     * @return OrderingEditorConfiguration
     */
    public static function create(?bool $vertical = null) : OrderingEditorConfiguration
    {
        $object = new OrderingEditorConfiguration();
        $object->vertical = $vertical;
        return $object;
    }

    /**
     * @return ?bool
     */
    public function isVertical() : ?bool
    {
        return $this->vertical;
    }
}
