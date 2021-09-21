<?php
declare(strict_types=1);

namespace srag\asq\Application\Command;

use Fluxlabs\CQRS\Aggregate\RevisionFactory;
use Fluxlabs\CQRS\Command\CommandContract;
use Fluxlabs\CQRS\Command\CommandHandlerContract;
use srag\asq\Domain\QuestionRepository;
use srag\asq\Infrastructure\Persistence\QuestionType;
use srag\asq\Infrastructure\Persistence\Projection\PublishedQuestionRepository;
use srag\asq\Application\Exception\AsqException;
use srag\asq\Domain\QuestionDto;
use ILIAS\Data\Result;
use ILIAS\Data\Result\Error;
use ILIAS\Data\Result\Ok;

/**
 * Class CreateQuestionRevisionCommandHandler
 *
 * Command handler for revision creation
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class CreateQuestionRevisionCommandHandler implements CommandHandlerContract
{
    /**
     * @param CreateQuestionRevisionCommand $command
     */
    public function handle(CommandContract $command) : Result
    {
        $repository = new PublishedQuestionRepository();

        if ($repository->revisionExists($command->getQuestionId(), $command->getRevisionName())) {
            return new Error(new AsqException(
                sprintf(
                   'A revision with the Name: "%s" already exists for Question: "%s"',
                   $command->getRevisionName(),
                   $command->getQuestionId()->toString()
               )
            ));
        }

        $repo = new QuestionRepository();
        $question = $repo->getAggregateRootById($command->getQuestionId());
        RevisionFactory::setRevisionId($question, $command->getRevisionName());
        $question_type = QuestionType::where(["title_key" => $question->getType()])->first();

        $repository->saveNewQuestionRevision(QuestionDto::CreateFromQuestion($question, $question_type));

        $repo->save($question);

        return new Ok(null);
    }
}
