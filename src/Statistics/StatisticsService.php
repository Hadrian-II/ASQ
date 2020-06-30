<?php
declare(strict_types=1);

namespace srag\asq\Statistics;

use ilDateTime;
use srag\asq\Application\Service\ASQService;

/**
 * Class StatisticsService
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class StatisticsService extends ASQService
{
    /**
     * @param string $question_id
     * @param string $question_version
     * @param string $context
     * @param int $user_id
     * @param float $points
     * @param ilDateTime $timestamp
     */
    public function registerScore(string $question_id, string $question_version, string $context, int $user_id, float $points, ilDateTime $timestamp) : void
    {
        $record = new StatisticsRecord($question_id, $question_version, $context, $user_id, $points, $timestamp);
        $record->create();
    }

    /**
     * @param string $question_id
     * @param string $question_version
     * @return object
     */
    public function getQuestionScoreStatistics(string $question_id, string $question_version) : object
    {
    }

    /**
     * @param string $question_id
     * @param string $question_version
     * @return object
     */
    public function getQuestionUsageStatistics(string $question_id, string $question_version) : object
    {
    }
}
