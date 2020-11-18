<?php
declare(strict_types=1);

namespace srag\asq\Questions\Ordering\Editor\Data;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class OrderingEditorConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class OrderingEditorConfiguration extends AbstractValueObject
{
    /**
     * @var ?bool
     */
    protected $vertical;

    /**
     * @param bool $vertical
     */
    public function __construct(?bool $vertical = null)
    {
        $this->vertical = $vertical;
    }

    /**
     * @return ?bool
     */
    public function isVertical() : ?bool
    {
        return $this->vertical;
    }
}
