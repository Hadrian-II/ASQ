<?php
declare(strict_types = 1);
namespace srag\asq\Domain\Model\Answer\Option;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class ImageAndTextDisplayDefinition
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 * @package srag/asq
 * @author Adrian Lüthi <al@studer-raimann.ch>
 */
class ImageAndTextDisplayDefinition extends AbstractValueObject
{
    /**
     * @var ?string
     */
    protected $text;

    /**
     * @var ?string
     */
    protected $image;

    /**
     * @param string $text
     * @param string $image
     * @return ImageAndTextDisplayDefinition
     */
    public static function create(?string $text = null, ?string $image = null) : ImageAndTextDisplayDefinition
    {
        $object = new ImageAndTextDisplayDefinition();
        $object->text = $text;
        $object->image = $image;
        return $object;
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
