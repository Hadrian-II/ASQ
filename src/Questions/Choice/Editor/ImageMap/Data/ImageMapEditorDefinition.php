<?php
declare(strict_types=1);

namespace srag\asq\Questions\Choice\Editor\ImageMap\Data;

use Fluxlabs\CQRS\Aggregate\AbstractValueObject;

/**
 * Class ImageMapEditorDefinition
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class ImageMapEditorDefinition extends AbstractValueObject
{
    const TYPE_RECTANGLE = 1;
    const TYPE_CIRCLE = 2;
    const TYPE_POLYGON = 3;

    protected ?string $tooltip;

    protected ?int $type;

    protected ?string $coordinates;

    public function __construct(?string $tooltip = null, ?int $type = null, ?string $coordinates = null)
    {
        $this->tooltip = $tooltip;
        $this->type = $type;
        $this->coordinates = $coordinates;
    }

    public function getTooltip() : ?string
    {
        return $this->tooltip;
    }

    public function getType() : ?int
    {
        return $this->type;
    }

    public function getCoordinates() : ?string
    {
        return $this->coordinates;
    }
}
