<?php
declare(strict_types = 1);
namespace srag\asq\Questions\Choice\Editor\ImageMap\Data;

use srag\asq\Domain\Model\Configuration\AbstractConfiguration;

/**
 * Class ImageMapEditorConfiguration
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 * @package srag/asq
 * @author Adrian Lüthi <al@studer-raimann.ch>
 */
class ImageMapEditorConfiguration extends AbstractConfiguration
{

    /**
     * @var ?string
     */
    protected $image;

    /**
     * @var ?bool
     */
    protected $multiple_choice;

    /**
     * @var ?int
     */
    protected $max_answers;

    /**
     * @param string $image
     * @return ImageMapEditorConfiguration
     */
    public static function create(
        ?string $image = null,
        ?bool $multiple_choice = true,
        ?int $max_answers = null
    ) : ImageMapEditorConfiguration {
        $object = new ImageMapEditorConfiguration();
        $object->image = $image;
        $object->multiple_choice = $multiple_choice;
        $object->max_answers = $max_answers;
        return $object;
    }

    /**
     * @return string|NULL
     */
    public function getImage() : ?string
    {
        return $this->image;
    }

    /**
     * @return bool|NULL
     */
    public function isMultipleChoice() : ?bool
    {
        return $this->multiple_choice;
    }

    /**
     * @return int|NULL
     */
    public function getMaxAnswers() : ?int
    {
        return $this->max_answers;
    }
}
