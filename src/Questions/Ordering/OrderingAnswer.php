<?php
declare(strict_types=1);

namespace srag\asq\Questions\Ordering;

use Fluxlabs\CQRS\Aggregate\AbstractValueObject;

/**
 * Class OrderingAnswer
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
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
