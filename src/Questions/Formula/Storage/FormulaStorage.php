<?php
declare(strict_types=1);

namespace srag\asq\Questions\Formula\Storage;

use srag\asq\Infrastructure\Persistence\RelationalEventStore\IQuestionStorage;

/**
 * Class FormulaStorage
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
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