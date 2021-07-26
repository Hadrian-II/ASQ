<?php
declare(strict_types = 1);
namespace srag\asq\Questions\Choice\Editor\ImageMap\Data;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class ImageMapEditorConfiguration
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 * @package srag/asq
 * @author Adrian Lüthi <al@studer-raimann.ch>
 */
class ImageMapEditorConfiguration extends AbstractValueObject
{
    protected ?string $image;

    protected ?bool $multiple_choice;

    protected ?int $max_answers;

    public function __construct(
        ?string $image = null,
        ?bool $multiple_choice = true,
        ?int $max_answers = null
    ) {
        $this->image = $image;
        $this->multiple_choice = $multiple_choice;
        $this->max_answers = $max_answers;
    }

    public function getImage() : ?string
    {
        return $this->image;
    }

    public function isMultipleChoice() : ?bool
    {
        return $this->multiple_choice;
    }

    public function getMaxAnswers() : ?int
    {
        return $this->max_answers;
    }
}
