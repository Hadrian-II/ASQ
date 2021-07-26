<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Generic\Data;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class ImageAndTextDisplayDefinition
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class ImageAndTextDisplayDefinition extends AbstractValueObject
{
    protected ?string $text;

    protected ?string $image;

    public function __construct(
        ?string $text = null,
        ?string $image = null
    ) {
        $this->text = $text;
        $this->image = $image;
    }

    public function getText() : ?string
    {
        return $this->text;
    }

    public function getImage() : ?string
    {
        return $this->image;
    }
}
