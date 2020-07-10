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
    /**
     * @var ?string
     */
    protected $id;

    /**
     * @var ?string
     */
    protected $text;

    /**
     * @var ?string
     */
    protected $image;

    /**
     * @param ?string $id
     * @param ?string $text
     * @param ?string $image
     * @return MatchingItem
     */
    public static function create(?string $id, ?string $text, ?string $image) : MatchingItem
    {
        $object = new MatchingItem();
        $object->id = $id;
        $object->text = $text;
        $object->image = $image;
        return $object;
    }

    /**
     * @return ?string
     */
    public function getId() : ?string
    {
        return $this->id;
    }

    /**
     * @return ?string
     */
    public function getText() : ?string
    {
        return $this->text;
    }

    /**
     * @return ?string
     */
    public function getImage() : ?string
    {
        return $this->image;
    }
}
