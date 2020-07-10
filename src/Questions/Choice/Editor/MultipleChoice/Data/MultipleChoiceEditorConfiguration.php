<?php
declare(strict_types = 1);
namespace srag\asq\Questions\MultipleChoice;

use srag\asq\Domain\Model\AbstractConfiguration;

/**
 * Class MultipleChoiceEditorConfiguration
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 * @package srag/asq
 * @author Adrian Lüthi <al@studer-raimann.ch>
 */
class MultipleChoiceEditorConfiguration extends AbstractConfiguration
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
     *
     * @param bool $single_line
     *
     * @return MultipleChoiceEditorConfiguration
     */
    public static function create(
        ?bool $shuffle_answers = false,
        ?int $max_answers = 1,
        ?int $thumbnail_size = null,
        ?bool $single_line = true
    ) : MultipleChoiceEditorConfiguration {
        $object = new MultipleChoiceEditorConfiguration();
        $object->shuffle_answers = $shuffle_answers;
        $object->max_answers = $max_answers;
        $object->thumbnail_size = $thumbnail_size;
        $object->single_line = $single_line;
        return $object;
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
