<?php
declare(strict_types=1);

namespace srag\asq\Questions\Cloze\Storage;

use srag\asq\Infrastructure\Persistence\RelationalEventStore\IQuestionStorage;

/**
 * Class ClozeStorage
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class ClozeStorage implements IQuestionStorage
{
    public function getPlayConfigurationHandler(): string
    {
        return ClozeConfigurationSetEventHandler::class;
    }

    public function getAnswerOptionHandler(): string
    {
        return ClozeAnswerOptionsSetEventHandler::class;
    }

    public function getSetup(): string
    {
        return SetupCloze::class;
    }
}