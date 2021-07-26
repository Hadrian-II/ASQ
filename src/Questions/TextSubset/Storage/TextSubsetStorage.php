<?php
declare(strict_types=1);

namespace srag\asq\Questions\TextSubset\Storage;

use srag\asq\Infrastructure\Persistence\RelationalEventStore\IQuestionStorage;

/**
 * Class TextSubsetStorage
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class TextSubsetStorage implements IQuestionStorage
{
    public function getPlayConfigurationHandler(): string
    {
        return TextSubsetConfigurationSetEventHandler::class;
    }

    public function getAnswerOptionHandler(): string
    {
        return TextSubsetAnswerOptionsSetEventHandler::class;
    }

    public function getSetup(): string
    {
        return SetupTextSubset::class;
    }
}