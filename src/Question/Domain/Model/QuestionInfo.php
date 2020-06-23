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
class QuestionInfo {
    /**
     * @var string
     */
    protected $revision_name;
    /**
     * @var string
     */
    protected $question_id;
    /**
     * @var string
     */
    protected $title;
    /**
     * @var string
     */
    protected $description;
    /**
     * @var string
     */
    protected $question;
    /**
     * @var string
     */
    protected $author;
    /**
     * @var int
     */
    protected $working_time;
    /**
     * @var ilDateTime
     */
    protected $created;
    
    public function __construct(QuestionListItemAr $question) {
        $this->author = $question->getAuthor();
        $this->created = $question->getCreated();
        $this->description = $question->getDescription();
        $this->question = $question->getQuestion();
        $this->question_id = $question->getQuestionId();
        $this->revision_name = $question->getRevisionName();
        $this->title = $question->getTitle();
        $this->working_time = $question->getWorkingTime();
    }
    
    /**
     * @return string
     */
    public function getTitle() : string
    {
        return $this->title;
    }
    
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return $this->description;
    }
    
    /**
     * @return string
     */
    public function getQuestion() : string
    {
        return $this->question;
    }
    
    /**
     * @return string
     */
    public function getAuthor() : string
    {
        return $this->author;
    }
    
    /**
     * @return int
     */
    public function getWorkingTime() : int
    {
        return $this->working_time;
    }
    
    /**
     * @return string
     */
    public function getQuestionId() : string {
        return $this->question_id;
    }
    
    /**
     * @return string
     */
    public function getRevisionName() : string {
        return $this->revision_name;
    }
    
    /**
     * @return ilDateTime
     */
    public function getCreated(): ilDateTime {
        return $this->created;
    }
}