<?php
declare(strict_types=1);

namespace srag\asq\Questions\Formula\Storage;

use srag\asq\Infrastructure\Persistence\RelationalEventStore\IQuestionStorage;

/**
 * Class FormulaStorage
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class FormulaStorage implements IQuestionStorage
{
    public function getPlayConfigurationHandler(): string
    {
        return FormulaConfigurationSetEventHandler::class;
    }

    public function getAnswerOptionHandler(): string
    {
        return FormulaAnswerOptionsSetEventHandler::class;
    }

    public function getSetup(): string
    {
        return SetupFormula::class;
    }
}