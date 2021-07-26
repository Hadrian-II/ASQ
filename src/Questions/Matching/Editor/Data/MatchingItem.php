<?php
declare(strict_types=1);

namespace srag\asq\Questions\Matching\Editor\Data;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class MatchingItem
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class MatchingItem extends AbstractValueObject
{
    protected ?string $id;

    protected ?string $text;

    protected ?string $image;

    public function __construct(?string $id = null, ?string $text = null, ?string $image = null)
    {
        $this->id = $id;
        $this->text = $text;
        $this->image = $image;
    }

    public function getId() : ?string
    {
        return $this->id;
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
