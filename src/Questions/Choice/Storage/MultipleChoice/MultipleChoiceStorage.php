<?php
declare(strict_types=1);

namespace srag\asq\Questions\Choice\Storage\MultipleChoice;

use srag\asq\Infrastructure\Persistence\RelationalEventStore\IQuestionStorage;

/**
 * Class MultipleChoiceStorage
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class MultipleChoiceStorage implements IQuestionStorage
{
    public function getPlayConfigurationHandler(): string
    {
        return MultipleChoiceConfigurationSetEventHandler::class;
    }

    public function getAnswerOptionHandler(): string
    {
        return MultipleChoiceAnswerOptionsSetEventHandler::class;
    }

    public function getSetup(): string
    {
        return SetupMultipleChoice::class;
    }
}