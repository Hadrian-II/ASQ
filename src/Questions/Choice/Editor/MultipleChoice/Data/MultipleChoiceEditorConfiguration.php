<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Choice\Editor\MultipleChoice\Data;

use Fluxlabs\CQRS\Aggregate\AbstractValueObject;

/**
 * Class MultipleChoiceEditorConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class MultipleChoiceEditorConfiguration extends AbstractValueObject
{
    protected ?bool $shuffle_answers;

    protected ?int $max_answers;

    protected ?int $thumbnail_size;

    protected ?bool $single_line;

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

    public function isShuffleAnswers() : ?bool
    {
        return $this->shuffle_answers;
    }

    public function getMaxAnswers() : ?int
    {
        return $this->max_answers;
    }

    public function getThumbnailSize() : ?int
    {
        return $this->thumbnail_size;
    }

    public function isSingleLine() : ?bool
    {
        return $this->single_line;
    }
}
