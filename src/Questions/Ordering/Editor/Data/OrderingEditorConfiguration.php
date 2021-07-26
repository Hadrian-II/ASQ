<?php
declare(strict_types=1);

namespace srag\asq\Questions\Ordering\Editor\Data;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class OrderingEditorConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class OrderingEditorConfiguration extends AbstractValueObject
{
    protected ?bool $vertical;

    protected ?string $text;

    public function __construct(?bool $vertical = null, ?string $text = null)
    {
        $this->vertical = $vertical;
        $this->text = $text;
    }

    public function isVertical() : ?bool
    {
        return $this->vertical;
    }

    public function getText() : ?string
    {
        return $this->text;
    }
}
