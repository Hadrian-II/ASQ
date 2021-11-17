<?php
declare(strict_types=1);

namespace srag\asq\Statistics;

use DateTimeImmutable;

/**
 * Class StatisticsService
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class StatisticsService
{
    public function registerScore(string $question_id, string $question_version, string $context, int $user_id, float $points, DateTimeImmutable $timestamp) : void
    {
        $record = new StatisticsRecord($question_id, $question_version, $context, $user_id, $points, $timestamp);
        $record->create();
    }

    public function getQuestionScoreStatistics(string $question_id, string $question_version) : object
    {
    }

    public function getQuestionUsageStatistics(string $question_id, string $question_version) : object
    {
    }
}
