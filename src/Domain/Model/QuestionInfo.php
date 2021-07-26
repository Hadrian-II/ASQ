<?php
declare(strict_types=1);

namespace srag\asq\Domain\Model;

use ilDateTime;
use srag\asq\Infrastructure\Persistence\Projection\QuestionListItemAr;

/**
 * Class QuestionInfo
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionInfo
{
    protected string $revision_name;

    protected string $question_id;

    protected string $title;

    protected string $description;

    protected string $question;

    protected string $author;

    protected int $working_time;

    protected ilDateTime $created;

    public function __construct(QuestionListItemAr $question)
    {
        $this->author = $question->getAuthor();
        $this->created = $question->getCreated();
        $this->description = $question->getDescription();
        $this->question = $question->getQuestion();
        $this->question_id = $question->getQuestionId();
        $this->revision_name = $question->getRevisionName();
        $this->title = $question->getTitle();
        $this->working_time = $question->getWorkingTime();
    }

    public function getTitle() : string
    {
        return $this->title;
    }

    public function getDescription() : string
    {
        return $this->description;
    }

    public function getQuestion() : string
    {
        return $this->question;
    }

    public function getAuthor() : string
    {
        return $this->author;
    }

    public function getWorkingTime() : int
    {
        return $this->working_time;
    }

    public function getQuestionId() : string
    {
        return $this->question_id;
    }

    public function getRevisionName() : string
    {
        return $this->revision_name;
    }

    public function getCreated() : ilDateTime
    {
        return $this->created;
    }
}
