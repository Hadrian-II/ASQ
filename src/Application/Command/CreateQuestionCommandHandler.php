<?php
declare(strict_types=1);

namespace srag\asq\Application\Command;

use srag\CQRS\Command\CommandContract;
use srag\CQRS\Command\CommandHandlerContract;
use srag\asq\Domain\QuestionRepository;
use srag\asq\Domain\Model\Question;

/**
 * Class CreateQuestionCommandHandler
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class CreateQuestionCommandHandler implements CommandHandlerContract {

    /**
     * @param CommandContract $command
     */
	public function handle(CommandContract $command) {
        /** @var CreateQuestionCommand $command */
	    
        /** @var Question $question */
		$question = Question::createNewQuestion(
			$command->getQuestionUuid(),
			$command->getIssuingUserId(),
		    $command->getQuestionType()
		);

		QuestionRepository::getInstance()->save($question);
	}
}