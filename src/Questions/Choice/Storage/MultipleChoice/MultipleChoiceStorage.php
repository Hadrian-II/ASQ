<?php
declare(strict_types=1);

namespace srag\asq\Questions\Choice\Storage\MultipleChoice;

use srag\asq\Infrastructure\Persistence\RelationalEventStore\IQuestionStorage;

/**
 * Class MultipleChoiceStorage
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
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