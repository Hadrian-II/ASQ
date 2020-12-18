<?php
declare(strict_types=1);

namespace srag\asq\Questions\Matching\Storage;

use srag\asq\Infrastructure\Persistence\RelationalEventStore\IQuestionStorage;

/**
 * Class MatchingStorage
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
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