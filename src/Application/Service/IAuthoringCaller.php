<?php
declare(strict_types=1);

namespace srag\asq\Application\Service;

use srag\asq\Domain\QuestionDto;

/**
 * interface IAuthoringCaller
 *
 * Interface Asq authoring calling objects have to implement,
 * so they can be notified about question creation
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
interface IAuthoringCaller
{
    public function afterQuestionCreated(QuestionDto $question) : void;
}
