<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Persistence\Projection;

use ActiveRecord;
use srag\asq\Domain\QuestionDto;
use DateTimeImmutable;
use ILIAS\Data\UUID\Uuid;
use ILIAS\Data\UUID\Factory;

/**
 * Class QuestionListItemAr
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class QuestionListItemAr extends ActiveRecord
{
    const STORAGE_NAME = "asq_question_list_item";

    public static function returnDbTableName() : string
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
     * @var DateTimeImmutable
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
        $object->created = new DateTimeImmutable();
        return $object;
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

    public function getQuestionId() : Uuid
    {
        return $this->question_id;
    }

    public function getRevisionName() : string
    {
        return $this->revision_name;
    }

    public function getCreated() : DateTimeImmutable
    {
        return $this->created;
    }

    public function wakeUp($field_name, $field_value)
    {
        switch ($field_name) {
            case 'created':
                $date = new DateTimeImmutable();
                return $date->setTimestamp($field_value);
            case 'id':
            case 'working_time':
                return intval($field_value);
            case 'question_id':
                $factory = new Factory();
                return $factory->fromString($field_value);
            default:
                return null;
        }
    }

    public function sleep($field_name)
    {
        switch ($field_name) {
            case 'created':
                return $this->created->getTimestamp();
            default:
                return null;
        }
    }
}
