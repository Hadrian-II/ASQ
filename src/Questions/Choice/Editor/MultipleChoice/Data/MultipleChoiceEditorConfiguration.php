<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Choice\Editor\MultipleChoice\Data;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class MultipleChoiceEditorConfiguration
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 * @package srag/asq
 * @author Adrian Lüthi <al@studer-raimann.ch>
 */
class MultipleChoiceEditorConfiguration extends AbstractValueObject
{

    /**
     * @var ?bool
     */
    protected $shuffle_answers;

    /**
     * @var ?int
     */
    protected $max_answers;

    /**
     * @var ?int
     */
    protected $thumbnail_size;

    /**
     * @var ?bool
     */
    protected $single_line;

    /**
     * @param bool $shuffle_answers
     * @param int $max_answers
     * @param int $thumbnail_size
     * @param bool $single_line
     */
    public function __construct(
        ?bool $shuffle_answers = false,
        ?int $max_answers = 1,
        ?int $thumbnail_size = null,
        ?bool $single_line = true
    ) {
        $this->shuffle_answers = $shuffle_answers;
        $this->max_answers = $max_answers;
        $this->thumbnail_size = $thumbnail_size;
        $this->single_line = $single_line;
    }

    /**
     * @return bool
     */
    public function isShuffleAnswers() : ?bool
    {
        return $this->shuffle_answers;
    }

    /**
     * @return int
     */
    public function getMaxAnswers() : ?int
    {
        return $this->max_answers;
    }

    /**
     * @return int
     */
    public function getThumbnailSize() : ?int
    {
        return $this->thumbnail_size;
    }

    /**
     * @return boolean
     */
    public function isSingleLine() : ?bool
    {
        return $this->single_line;
    }
}
