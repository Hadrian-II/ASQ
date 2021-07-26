<?php
declare(strict_types=1);

namespace srag\asq\Questions\ErrorText\Storage;

use srag\asq\Infrastructure\Persistence\RelationalEventStore\IQuestionStorage;

/**
 * Class ErrorTextStorage
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class ErrorTextStorage implements IQuestionStorage
{
    public function getPlayConfigurationHandler(): string
    {
        return ErrorTextConfigurationSetEventHandler::class;
    }

    public function getAnswerOptionHandler(): string
    {
        return ErrorTextAnswerOptionsSetEventHandler::class;
    }

    public function getSetup(): string
    {
        return SetupErrorText::class;
    }
}