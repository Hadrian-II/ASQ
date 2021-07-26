<?php
declare(strict_types=1);

namespace srag\asq\Domain\Model;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class QuestionData
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionData extends AbstractValueObject
{
    const LIFECYCLE_DRAFT = 1;
    const LIFECYCLE_TO_BE_REVIEWED = 2;
    const LIFECYCLE_REJECTED = 3;
    const LIFECYCLE_FINAL = 4;
    const LIFECYCLE_SHARABLE = 5;
    const LIFECYCLE_OUTDATED = 6;

    protected ?string $title;

    protected ?string $description;

    protected ?int $lifecycle = self::LIFECYCLE_DRAFT;

    protected ?string $question_text;

    protected ?string $author;

    protected ?int $working_time = 0;

    public function __construct(
        ?string $title = null,
        ?string $text = null,
        ?string $author = null,
        ?string $description = null,
        ?int $working_time = null,
        ?int $lifecycle = self::LIFECYCLE_DRAFT
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->question_text = $text;
        $this->author = $author;
        $this->working_time = $working_time;
        $this->lifecycle = $lifecycle;
    }

    public function getTitle() : ?string
    {
        return $this->title;
    }

    public function getDescription() : ?string
    {
        return $this->description;
    }

    public function getLifecycle() : ?int
    {
        return $this->lifecycle;
    }

    public function getQuestionText() : ?string
    {
        return $this->question_text;
    }

    public function getAuthor() : ?string
    {
        return $this->author;
    }

    public function getWorkingTime() : ?int
    {
        return $this->working_time;
    }

    public function isComplete() : bool
    {
        return !empty($this->title) &&
               !empty($this->working_time) &&
               !empty($this->author) &&
               !empty($this->question_text) &&
               !empty($this->lifecycle);
    }

    public function equals(AbstractValueObject $other) : bool
    {
        /** @var QuestionData $other */
        return get_class($this) === get_class($other) &&
               $this->getAuthor() === $other->getAuthor() &&
               $this->getDescription() === $other->getDescription() &&
               $this->getLifecycle() === $other->getLifecycle() &&
               $this->getQuestionText() === $other->getQuestionText() &&
               $this->getTitle() === $other->getTitle() &&
               $this->getWorkingTime() === $other->getWorkingTime();
    }
}
