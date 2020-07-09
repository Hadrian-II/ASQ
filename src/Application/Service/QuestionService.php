<?php
declare(strict_types=1);

namespace srag\asq\Application\Service;

use srag\CQRS\Command\CommandBus;
use srag\CQRS\Command\CommandConfiguration;
use srag\CQRS\Command\Access\OpenAccess;
use srag\asq\Application\Command\CreateQuestionCommand;
use srag\asq\Application\Command\CreateQuestionCommandHandler;
use srag\asq\Application\Command\CreateQuestionRevisionCommand;
use srag\asq\Application\Command\CreateQuestionRevisionCommandHandler;
use srag\asq\Application\Command\SaveQuestionCommand;
use srag\asq\Application\Command\SaveQuestionCommandHandler;
use srag\asq\Application\Exception\AsqException;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\QuestionRepository;
use srag\asq\Domain\Model\QuestionTypeDefinition;
use srag\asq\Infrastructure\Persistence\QuestionType;
use srag\asq\Infrastructure\Persistence\Projection\PublishedQuestionRepository;
use ILIAS\Data\UUID\Factory;

/**
 * Class QuestionService
 *
 * Main Question service, profiding methods for question manipulation
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionService extends ASQService
{
    /**
     * @var CommandBus
     */
    private $command_bus;

    /**
     * @return CommandBus
     */
    private function getCommandBus() : CommandBus
    {
        if (is_null($this->command_bus)) {
            $this->command_bus = new CommandBus();

            $this->command_bus->registerCommand(new CommandConfiguration(
                CreateQuestionCommand::class,
                new CreateQuestionCommandHandler(),
                new OpenAccess()
            ));

            $this->command_bus->registerCommand(new CommandConfiguration(
                CreateQuestionRevisionCommand::class,
                new CreateQuestionRevisionCommandHandler(),
                new OpenAccess()
            ));

            $this->command_bus->registerCommand(new CommandConfiguration(
                SaveQuestionCommand::class,
                new SaveQuestionCommandHandler(),
                new OpenAccess()
            ));
        }

        return $this->command_bus;
    }

    /**
     * Gets question dto by question id
     *
     * @param string $id
     * @throws AsqException
     * @return QuestionDto
     */
    public function getQuestionByQuestionId(string $id) : QuestionDto
    {
        $question = QuestionRepository::getInstance()->getAggregateRootById($id);

        return QuestionDto::CreateFromQuestion($question);
    }

    /**
     * Gets question revision by question id and revision name
     *
     * @param string $id
     * @param string $name
     * @return QuestionDto
     */
    public function getQuestionRevision(string $id, string $name) : QuestionDto
    {
        $repo = new PublishedQuestionRepository();
        return $repo->getQuestionRevision($id, $name);
    }

    /**
     * Gets list of all created revisions of a question
     *
     * @param string $id
     * @return array
     */
    public function getAllRevisionsOfQuestion(string $id) : array
    {
        $repo = new PublishedQuestionRepository();
        return $repo->getAllQuestionRevisions($id);
    }

    /**
     * Create anew revision named $name for question with id $question_id
     *
     * @param string $name
     * @param string $question_id
     */
    public function createQuestionRevision(string $name, string $question_id)
    {
        $this->getCommandBus()->handle(new CreateQuestionRevisionCommand($question_id, $name, $this->getActiveUser()));
    }

    /**
     * Creates a new question with given type
     *
     * @param int $type
     * @param ?int $container_id
     *
     * @return QuestionDto
     */
    public function createQuestion(QuestionTypeDefinition $type) : QuestionDto
    {
        $uuid_factory = new Factory();

        $id = $uuid_factory->uuid4AsString();

        $this->getCommandBus()->handle(
            new CreateQuestionCommand(
                $id,
                $type,
                $this->getActiveUser()
            )
        );

        return $this->getQuestionByQuestionId($id);
    }

    /**
     * Saves changes to a question
     *
     * @param QuestionDto $question_dto
     */
    public function saveQuestion(QuestionDto $question_dto)
    {
        // check changes and trigger them on question if there are any
        $question = QuestionRepository::getInstance()->getAggregateRootById($question_dto->getId());

        $question->setData($question_dto->getData(), $this->getActiveUser());
        $question->setPlayConfiguration($question_dto->getPlayConfiguration(), $this->getActiveUser());
        $question->setAnswerOptions($question_dto->getAnswerOptions(), $this->getActiveUser());
        $question->setFeedback($question_dto->getFeedback(), $this->getActiveUser());
        $question->setHints($question_dto->getQuestionHints(), $this->getActiveUser());

        if (count($question->getRecordedEvents()->getEvents()) > 0) {
            // save changes if there are any
            $this->getCommandBus()->handle(new SaveQuestionCommand($question, $this->getActiveUser()));
        }
    }

    /**
     * Gets a list of all available question types
     *
     * @return QuestionTypeDefinition[]
     */
    public function getAvailableQuestionTypes() : array
    {
        return array_map(function ($type) {
            return QuestionTypeDefinition::create($type);
        }, QuestionType::get());
    }

    /**
     * Add a new question type
     *
     * @param string $title_key
     * @param string $form_class
     */
    public function addQuestionType(string $title_key, string $factory_class)
    {
        $type = QuestionType::createNew($title_key, $factory_class);
        $type->create();
    }

    /**
     * Remova an existing question type
     *
     * @param string $form_class
     */
    public function removeQuestionType(string $form_class)
    {
        QuestionType::where(['form_class' => $form_class])->first()->delete();
    }
}
