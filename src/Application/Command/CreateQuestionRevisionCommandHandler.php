<?php
declare(strict_types=1);

namespace srag\asq\Application\Command;

use srag\CQRS\Aggregate\RevisionFactory;
use srag\CQRS\Command\CommandContract;
use srag\CQRS\Command\CommandHandlerContract;
use srag\asq\Domain\QuestionRepository;
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
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class CreateQuestionRevisionCommandHandler implements CommandHandlerContract
{

    /**
     * @param CommandContract $command
     */
    public function handle(CommandContract $command) : Result
    {
        /** @var CreateQuestionRevisionCommand $command */
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


        $question = QuestionRepository::getInstance()->getAggregateRootById($command->getQuestionId());
        RevisionFactory::setRevisionId($question, $command->getRevisionName());

        $repository->saveNewQuestionRevision(QuestionDto::CreateFromQuestion($question));

        QuestionRepository::getInstance()->save($question);

        return new Ok(null);
    }
}
