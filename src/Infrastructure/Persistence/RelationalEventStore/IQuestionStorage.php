<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Persistence\RelationalEventStore;

use srag\asq\Infrastructure\Persistence\RelationalEventStore\Setup\IQuestionDBSetup;

/**
 * Interface IQuestionStorage
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
interface IQuestionStorage
{
    public function getPlayConfigurationHandler() : string;

    public function getAnswerOptionHandler() : string;

    public function getSetup() : string;
}