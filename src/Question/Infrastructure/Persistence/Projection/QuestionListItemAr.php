<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Persistence\Projection;

use ActiveRecord;
use ilDateTimeException;
use srag\asq\Domain\QuestionDto;
use ilDateTime;

/**
 * Class QuestionListItemAr
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionListItemAr extends ActiveRecord
{

    const STORAGE_NAME = "asq_question_list_item";

    /**
     * @return string
     */
    static function returnDbTableName()
    {
        return self::STORAGE_NAME;
    }
    
    /**
     * @var int
     *
     * @con_is_primary true
     * @con_is_unique  true
     * @con_has_field  true
     * @con_fieldtype  integer
     * @con_length     8
     * @con_sequence   true
     */
    protected $id;
    /**
     * @var string
     *
     * @con_has_field  true
     * @con_fieldtype  text
     * @con_length     200
     * @con_index      true
     * @con_is_notnull true
     */
    protected $revision_name;
    /**
     * @var string
     *
     * @con_has_field  true
     * @con_fieldtype  text
     * @con_length     200
     * @con_index      true
     * @con_is_notnull true
     */
    protected $question_id;
    /**
     * @var string
     *
     * @con_has_field  true
     * @con_fieldtype  text
     * @con_length     200
     * @con_is_notnull true
     */
    protected $title;
    /**
     * @var string
     *
     * @con_has_field  true
     * @con_fieldtype  text
     * @con_length     400
     */
    protected $description;
    /**
     * @var string
     *
     * @con_has_field  true
     * @con_fieldtype  text
     * @con_length     200
     */
    protected $question;
    /**
     * @var string
     *
     * @con_has_field  true
     * @con_fieldtype  text
     * @con_length     200
     */
    protected $author;
    /**
     * @var int
     *
     * @con_has_field  true
     * @con_fieldtype  integer
     */
    protected $working_time;
    /**
     * @var ilDateTime
     * 
     * @con_has_field true
     * @con_fieldtype timestamp
     */
    protected $created;
    
    public static function createNew(QuestionDto $question) : QuestionListItemAr 
    {
        $object = new QuestionListItemAr();
        $object->question_id = $question->getId();
        $object->revision_name = $question->getRevisionId()->getName();
        $object->title = $question->getData()->getTitle();
        $object->description = $question->getData()->getDescription();
        $object->question = $question->getData()->getQuestionText();
        $object->author = $question->getData()->getAuthor();
        $object->working_time = $question->getData()->getWorkingTime();
        $object->created = new ilDateTime(time(), IL_CAL_UNIX);
        return $object;
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
    
    /**
     * @param $field_name
     * @param $field_value
     *
     * @return ilDateTime|mixed|null
     * @throws ilDateTimeException
     */
    public function wakeUp($field_name, $field_value)
    {
        switch ($field_name) {
            case 'created':
                return new ilDateTime($field_value, IL_CAL_DATETIME);
            case 'id':
            case 'working_time':
                return intval($field_value);
            default:
                return null;
        }
    }
}