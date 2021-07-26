<?php
declare(strict_types=1);

namespace srag\asq\Questions\Ordering;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class OrderingAnswer
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class OrderingAnswer extends AbstractValueObject
{
    /**
     * @var ?int[]
     */
    protected ?array $selected_order;

    public function __construct(?array $selected_order = [])
    {
        $this->selected_order = $selected_order;
    }

    public function getSelectedOrder() : ?array
    {
        return $this->selected_order;
    }
}
