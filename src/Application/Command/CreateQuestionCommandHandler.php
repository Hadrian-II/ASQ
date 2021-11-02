<?php
declare(strict_types=1);

namespace srag\asq\Application\Command;

use Fluxlabs\CQRS\Command\CommandContract;
use Fluxlabs\CQRS\Command\CommandHandlerContract;
use srag\asq\Domain\QuestionRepository;
use srag\asq\Domain\Model\Question;
use ILIAS\Data\Result;
use ILIAS\Data\Result\Ok;

/**
 * Class CreateQuestionCommandHandler
 *
 * Command Handler for Question Creation
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class CreateQuestionCommandHandler implements CommandHandlerContract
{

    /**
     * @param CreateQuestionCommand $command
     */
    public function handle(CommandContract $command) : Result
    {
        $question = Question::createNewQuestion(
            $command->getQuestionUuid(),
            $command->getQuestionType()
        );

        $repo = new QuestionRepository();
        $repo->save($question);

        return new Ok(null);
    }
}
