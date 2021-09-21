<?php
declare(strict_types=1);

namespace srag\asq\Application\Command;

use Fluxlabs\CQRS\Command\CommandContract;
use Fluxlabs\CQRS\Command\CommandHandlerContract;
use srag\asq\Domain\QuestionRepository;
use ILIAS\Data\Result;
use ILIAS\Data\Result\Ok;

/**
 * Class SaveQuestionCommandHandler
 *
 * Eventhandler for saving Question
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class SaveQuestionCommandHandler implements CommandHandlerContract
{
    /**
     * @param SaveQuestionCommand $command
     */
    public function handle(CommandContract $command) : Result
    {
        $repo = new QuestionRepository();

        $repo->save($command->GetQuestion());

        return new Ok(null);
    }
}
