<?php
declare(strict_types=1);

namespace srag\asq\Questions\Matching\Storage;

use srag\asq\Infrastructure\Persistence\RelationalEventStore\IQuestionStorage;

/**
 * Class MatchingStorage
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class MatchingStorage implements IQuestionStorage
{
    public function getPlayConfigurationHandler(): string
    {
        return MatchingConfigurationSetEventHandler::class;
    }

    public function getAnswerOptionHandler(): string
    {
        // not used
        return '';
    }

    public function getSetup(): string
    {
        return SetupMatching::class;
    }
}