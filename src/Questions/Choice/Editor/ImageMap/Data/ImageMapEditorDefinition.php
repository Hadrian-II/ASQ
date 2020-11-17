<?php
declare(strict_types=1);

namespace srag\asq\Questions\Choice\Editor\ImageMap\Data;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class ImageMapEditorDefinition
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class ImageMapEditorDefinition extends AbstractValueObject
{
    const TYPE_RECTANGLE = 1;
    const TYPE_CIRCLE = 2;
    const TYPE_POLYGON = 3;

    /**
     * @var ?string
     */
    protected $tooltip;

    /**
     * @var ?int
     */
    protected $type;

    /**
     * @var ?string
     */
    protected $coordinates;

    /**
     *
     * @param string $tooltip
     * @param int $type
     * @param string $coordinates
     */
    public function __construct(?string $tooltip = null, ?int $type = null, ?string $coordinates = null)
    {
        $this->tooltip = $tooltip;
        $this->type = $type;
        $this->coordinates = $coordinates;
    }

    /**
     * @return string
     */
    public function getTooltip() : ?string
    {
        return $this->tooltip;
    }

    /**
     * @return int
     */
    public function getType() : ?int
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getCoordinates() : ?string
    {
        return $this->coordinates;
    }
}
