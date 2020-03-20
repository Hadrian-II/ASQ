<?php
declare(strict_types=1);

namespace srag\asq\Application\Service;

use srag\asq\Domain\QuestionDto;

/**
 * interface IAuthoringCaller
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
interface IAuthoringCaller
{
    public function afterQuestionCreated(QuestionDto $question);
}