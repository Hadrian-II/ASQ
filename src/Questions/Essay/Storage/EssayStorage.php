<?php
declare(strict_types=1);

namespace srag\asq\Questions\Essay\Storage;

use srag\asq\Infrastructure\Persistence\RelationalEventStore\IQuestionStorage;

/**
 * Class EssayStorage
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class EssayStorage implements IQuestionStorage
{
    public function getPlayConfigurationHandler(): string
    {
        return EssayConfigurationSetEventHandler::class;
    }

    public function getAnswerOptionHandler(): string
    {
        return EssayAnswerOptionsSetEventHandler::class;
    }

    public function getSetup(): string
    {
        return SetupEssay::class;
    }
}