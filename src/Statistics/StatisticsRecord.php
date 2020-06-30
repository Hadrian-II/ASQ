<?php
declare(strict_types=1);

namespace srag\asq\Statistics;

use ActiveRecord;
use ilDateTime;

/**
 * Class StatisticsRecord
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class StatisticsRecord extends ActiveRecord
{
    const STORAGE_NAME = "asq_statistics_record";

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
     * @con_length     40
     * @con_index      true
     * @con_is_notnull true
     */
    protected $question_id;
    /**
     * @var string
     *
     * @con_has_field  true
     * @con_fieldtype  text
     * @con_length     80
     * @con_index      true
     * @con_is_notnull true
     */
    protected $question_version;
    /**
     * @var string
     *
     * @con_has_field  true
     * @con_fieldtype  text
     * @con_length     120
     */
    protected $context;
    /**
     * @var int
     *
     * @con_has_field  true
     * @con_fieldtype  integer
     * @con_is_notnull true
     */
    protected $user_id;
    /**
     * @var float
     *
     * @con_has_field  true
     * @con_fieldtype  float
     * @con_is_notnull true
     */
    protected $points;
    /**
     * @var int
     *
     * @con_has_field  true
     * @con_fieldtype  timestamp
     * @con_is_notnull true
     */
    protected $timestamp;

    /**
     * @param string $question_id
     * @param string $question_version
     * @param string $context
     * @param int $user
     * @param float $points
     * @param int $timestamp
     */
    public function __construct(
        string $question_id,
        string $question_version,
        string $context,
        int $user_id,
        float $points,
        ilDateTime $timestamp
    ) {
        parent::__construct();

        $this->question_id = $question_id;
        $this->question_version = $question_version;
        $this->context = $context;
        $this->user_id = $user_id;
        $this->points = $points;
        $this->timestamp = $timestamp->getUnixTime();
    }

    /**
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getQuestionId() : string
    {
        return $this->question_id;
    }

    /**
     * @return string
     */
    public function getContext() : string
    {
        return $this->context;
    }

    /**
     * @return int
     */
    public function getUserId() : int
    {
        return $this->user_id;
    }

    /**
     * @return float
     */
    public function getPoints() : float
    {
        return $this->points;
    }

    /**
     * @return ilDateTime
     */
    public function getTimestamp() : ilDateTime
    {
        return new ilDateTime($this->timestamp, IL_CAL_UNIX);
    }

    /**
     * @return string
     */
    public static function returnDbTableName()
    {
        return self::STORAGE_NAME;
    }
}
