<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Persistence\Projection;

use ActiveRecord;
use ilDateTime;
use srag\asq\Domain\QuestionDto;

/**
 * Class QuestionAr
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class QuestionAr extends ActiveRecord
{
    const ACTIVE = 1;
    const DELETED = 2;

    const STORAGE_NAME = "asq_question";
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
     * @con_has_field true
     * @con_fieldtype timestamp
     */
    protected $created;
    /**
     * @var int
     *
     * @con_has_field  true
     * @con_fieldtype  integer
     * @con_length     8
     */
    protected $creator;
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
     * @con_index      true
     * @con_is_notnull true
     */
    protected $revision_name;

    /**
     * @var string
     *
     * @con_has_field  true
     * @con_fieldtype  clob
     * @con_is_notnull true
     */
    protected $data;
    /**
     * @var int
     *
     * @con_has_field  true
     * @con_fieldtype  integer
     * @con_length     8
     * @con_is_notnull true
     */
    protected $status;

    public static function createNew(QuestionDto $question) : QuestionAr
    {
        global $DIC;
        $object = new QuestionAr();

        $created = new ilDateTime(time(), IL_CAL_UNIX);
        $object->created = $created->get(IL_CAL_DATETIME);
        $object->creator = $DIC->user()->getId();
        $object->question_id = $question->getId();
        $object->revision_name = $question->getRevisionId()->getName();
        $object->data = json_encode($question);
        $object->status = self::ACTIVE;

        return $object;
    }

    public function getId() : int
    {
        return $this->id;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function getCreator() : int
    {
        return $this->creator;
    }

    public function getQuestionId() : string
    {
        return $this->question_id;
    }

    public function getRevisionName() : string
    {
        return $this->revision_name;
    }

    public function getQuestion() : QuestionDto
    {
        return QuestionDto::deserialize($this->data);
    }

    public function getStatus() : int
    {
        return $this->status;
    }

    public function delete() : void
    {
        $this->status = self::DELETED;
        $this->update();
    }

    public static function returnDbTableName() : string
    {
        return self::STORAGE_NAME;
    }
}
