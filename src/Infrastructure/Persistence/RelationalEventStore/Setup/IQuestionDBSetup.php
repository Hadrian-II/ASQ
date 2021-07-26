<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Persistence\RelationalEventStore\Setup;

/**
 * Interface IQuestionDBSetup
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
interface IQuestionDBSetup
{
    public function setup() : void;

    public function drop() : void;
}