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
     * @con_fieldtype integer
     */
    protected $created;

    public static function createNew(QuestionDto $question) : QuestionListItemAr
    {
        $object = new QuestionListItemAr();
        $object->updateQuestion($question);
        return $object;
    }

    public function updateQuestion(QuestionDto $question) : void
    {
        $this->question_id = $question->getId();
        $this->revision_name = $question->getRevisionId() ? $question->getRevisionId()->getName() : '';

        if ($question->getData() !== null) {
            $this->title = $question->getData()->getTitle();
            $this->description = $question->getData()->getDescription();
            $this->question = $question->getData()->getQuestionText();
            $this->author = $question->getData()->getAuthor();
            $this->working_time = $question->getData()->getWorkingTime();
        }
        else {
            $this->title = '';
        }

        $this->created = new DateTimeImmutable();
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
                return $field_value ? (new DateTimeImmutable())->setTimestamp(intval($field_value)) : null;
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
                return $this->created ? $this->created->getTimestamp() : null;
            case 'question_id':
                return $this->question_id ? $this->question_id->toString() : null;
            default:
                return null;
        }
    }
}
