<?php
declare(strict_types=1);

namespace srag\asq\Application\Service;

use ILIAS\Data\UUID\Factory;
use ILIAS\Data\UUID\Uuid;
use Fluxlabs\CQRS\Command\CommandBus;
use Fluxlabs\CQRS\Command\CommandConfiguration;
use Fluxlabs\CQRS\Command\Access\OpenAccess;
use srag\asq\Application\Command\CreateQuestionCommand;
use srag\asq\Application\Command\CreateQuestionCommandHandler;
use srag\asq\Application\Command\CreateQuestionRevisionCommand;
use srag\asq\Application\Command\CreateQuestionRevisionCommandHandler;
use srag\asq\Application\Command\SaveQuestionCommand;
use srag\asq\Application\Command\SaveQuestionCommandHandler;
use srag\asq\Application\Exception\AsqException;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\QuestionRepository;
use srag\asq\Domain\Model\Question;
use srag\asq\Infrastructure\Persistence\QuestionType;
use srag\asq\Infrastructure\Persistence\Projection\PublishedQuestionRepository;
use srag\asq\Application\Command\DeleteQuestionRevisionCommand;
use srag\asq\Application\Command\DeleteQuestionRevisionCommandHandler;

/**
 * Class QuestionService
 *
 * Main Question service, profiding methods for question manipulation
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class QuestionService
{
    private CommandBus $command_bus;

    private QuestionRepository $repo;

    public function __construct()
    {
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
            DeleteQuestionRevisionCommand::class,
            new DeleteQuestionRevisionCommandHandler(),
            new OpenAccess()
            ));

        $this->command_bus->registerCommand(new CommandConfiguration(
            SaveQuestionCommand::class,
            new SaveQuestionCommandHandler(),
            new OpenAccess()
        ));

        $this->repo = new QuestionRepository();
    }

    /**
     * Gets question dto by question id
     *
     * @param Uuid $id
     * @throws AsqException
     * @return QuestionDto
     */
    public function getQuestionByQuestionId(Uuid $id) : QuestionDto
    {
        /** @var $question Question */
        $question = $this->repo->getAggregateRootById($id);

        $question_type = QuestionType::where(["title_key" => $question->getType()])->first();

        if (is_null($question_type)) {
            throw new AsqException(sprintf('Unknown Question Type "%s"', $question->getType()));
        }

        return QuestionDto::CreateFromQuestion($question, $question_type);
    }

    /**
     * Gets question revision by question id and revision name
     *
     * @param Uuid $id
     * @param string $name
     * @return QuestionDto
     */
    public function getQuestionRevision(Uuid $id, string $name) : QuestionDto
    {
        $repo = new PublishedQuestionRepository();
        return $repo->getQuestionRevision($id, $name);
    }

    /**
     * Gets list of all created revisions of a question
     *
     * @param Uuid $id
     * @return array
     */
    public function getAllRevisionsOfQuestion(Uuid $id) : array
    {
        $repo = new PublishedQuestionRepository();
        return $repo->getAllQuestionRevisions($id);
    }

    /**
     * Create a new revision named $name for question with id $question_id
     *
     * @param string $name
     * @param Uuid $question_id
     * @throws AsqException
     */
    public function createQuestionRevision(string $name, Uuid $question_id) : void
    {
        $result = $this->command_bus->handle(new CreateQuestionRevisionCommand($question_id, $name));

        if ($result->isError()) {
            throw new AsqException($result->value());
        }
    }

    /**
     * Delete revision named $name for question with id $question_id
     *
     * @param string $name
     * @param Uuid $question_id
     * @throws AsqException
     */
    public function deleteQuestionRevision(string $name, Uuid $question_id) : void
    {
        $result = $this->command_bus->handle(new DeleteQuestionRevisionCommand($question_id, $name));

        if ($result->isError()) {
            throw new AsqException($result->value());
        }
    }

    /**
     * Creates a new question with given type
     *
     * @param QuestionType $type
     * @return QuestionDto
     * @throws AsqException
     */
    public function createQuestion(QuestionType $type) : QuestionDto
    {
        $uuid_factory = new Factory();

        $id = $uuid_factory->uuid4();

        $this->command_bus->handle(
            new CreateQuestionCommand(
                $id,
                $type
            )
        );

        return $this->getQuestionByQuestionId($id);
    }

    /**
     * Saves changes to a question
     *
     * @param QuestionDto $question_dto
     */
    public function saveQuestion(QuestionDto $question_dto) : void
    {
        // check changes and trigger them on question if there are any
        $question = $this->repo->getAggregateRootById($question_dto->getId());

        $question->setData($question_dto->getData());
        $question->setPlayConfiguration($question_dto->getPlayConfiguration());
        $question->setAnswerOptions($question_dto->getAnswerOptions());
        $question->setFeedback($question_dto->getFeedback());
        $question->setHints($question_dto->getQuestionHints());
        $question->setMetadata($question_dto->getMetadata());

        if (count($question->getRecordedEvents()->getEvents()) > 0) {
            // save changes if there are any
            $this->command_bus->handle(new SaveQuestionCommand($question));
        }
    }

    /**
     * Gets a list of all available question types
     *
     * @return QuestionType[]
     */
    public function getAvailableQuestionTypes() : array
    {
        return QuestionType::get();
    }

    /**
     * Add a new question type
     *
     * @param string $title_key
     * @param string $factory_class
     * @param string $editor_class
     * @param string $scoring_class
     * @param string $storage_class
     * @throws AsqException
     */
    public function addQuestionType(
        string $title_key,
        string $factory_class,
        string $editor_class,
        string $scoring_class,
        string $storage_class
    ) : void {
        if(QuestionType::where(['title_key' => $title_key])->count() > 0) {
            throw new AsqException(sprintf('Question Type: "%s" already exists', $title_key));
        }

        $type = QuestionType::createNew($title_key, $factory_class, $editor_class, $scoring_class, $storage_class);
        $type->create();
    }

    /**
     * Remova an existing question type
     *
     * @param string $title_key
     */
    public function removeQuestionType(string $title_key) : void
    {
        QuestionType::where(['title_key' => $title_key])->first()->delete();
    }

    /**
     * Exports a Question as JSON
     *
     * @param Uuid $id
     * @param string|null $revision
     * @return string
     * @throws AsqException
     */
    public function exportQuestion(Uuid $id, ?string $revision = null) : string
    {
        if ($revision === null) {
            $question = $this->getQuestionByQuestionId($id);
        }
        else {
            $question = $this->getQuestionRevision($id, $revision);
        }

        return json_encode($question);
    }

    /**
     * Imports a question from JSON
     *
     * @param string $json
     * @throws AsqException
     * @return QuestionDto
     */
    public function importQuestion(string $json) : QuestionDto
    {
        $decoded = json_decode($json, true);

        if (is_null($decoded)) {
            throw new AsqException(sprintf('JSON decoding failed with message: "%s"', json_last_error_msg()));
        }

        $dto = QuestionDto::deserialize($json);

        if ($this->repo->aggregateExists($dto->getId())) {
            throw new AsqException(sprintf('Question with Id: "%s" already in the System', $dto->getId()->toString()));
        }

        $this->command_bus->handle(
            new CreateQuestionCommand(
                $dto->getId(),
                $dto->getType(),
                $this->getActiveUser()
                )
            );

        $this->saveQuestion($dto);

        return $this->getQuestionByQuestionId($dto->getId());
    }
}
