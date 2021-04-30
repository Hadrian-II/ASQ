<?php
declare(strict_types=1);

namespace srag\asq\Application\Command;

use ILIAS\Data\Result;
use ILIAS\Data\Result\Error;
use ILIAS\Data\Result\Ok;
use srag\CQRS\Command\CommandContract;
use srag\CQRS\Command\CommandHandlerContract;
use srag\asq\Application\Exception\AsqException;
use srag\asq\Infrastructure\Persistence\Projection\PublishedQuestionRepository;

/**
 * Class DeleteQuestionRevisionCommandHandler
 *
 * Command handler for revision creation
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class DeleteQuestionRevisionCommandHandler implements CommandHandlerContract
{

    /**
     * @param CreateQuestionRevisionCommand $command
     */
    public function handle(CommandContract $command) : Result
    {
        $repository = new PublishedQuestionRepository();

        if (!$repository->revisionExists($command->getQuestionId(), $command->getRevisionName())) {
            return new Error(new AsqException(
                sprintf(
                    'A revision with the Name: "%s" does not exist for Question: "%s"',
                    $command->getRevisionName(),
                    $command->getQuestionId()->toString()
                    )
                ));
        }

        $repository->deleteQuestionRevision($command->getQuestionId(), $command->getRevisionName());

        return new Ok(null);
    }
}
