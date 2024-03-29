<?php
declare(strict_types=1);

namespace srag\asq\Questions\Ordering\Storage;

use DateTimeImmutable;
use Fluxlabs\CQRS\Event\DomainEvent;
use srag\asq\Domain\Event\QuestionAnswerOptionsSetEvent;
use srag\asq\Domain\Model\Answer\Option\AnswerOption;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\AbstractEventStorageHandler;
use srag\asq\Questions\Generic\Data\EmptyDefinition;
use srag\asq\Questions\Generic\Data\ImageAndTextDisplayDefinition;

/**
 * Class OrderingAnswerOptionsSetEventHandler
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class OrderingAnswerOptionsSetEventHandler extends AbstractEventStorageHandler
{
    public function handleEvent(DomainEvent $event, int $event_id) : void
    {
        /** @var $answer_options AnswerOption[] */
        $answer_options = $event->getAnswerOptions();

        foreach ($answer_options as $option) {
            $answer_id = intval($this->db->nextId(SetupOrdering::TABLENAME_ORDERING_ANSWER));
            $this->db->insert(SetupOrdering::TABLENAME_ORDERING_ANSWER, [
                'answer_id' => ['integer', $answer_id],
                'event_id' => ['integer', $event_id],
                'text' => ['clob', $option->getDisplayDefinition()->getText()],
                'image' => ['text', $option->getDisplayDefinition()->getImage()]
            ]);
        }
    }

    public function getQueryString(): string
    {
        return 'select * from ' . SetupOrdering::TABLENAME_ORDERING_ANSWER . ' where event_id in(%s)';
    }

    public function createEvent(array $data, array $rows): DomainEvent
    {
        $id = 1;
        $options = [];
        foreach ($rows as $row) {
            $options[] = new AnswerOption(
                strval($id),
                new ImageAndTextDisplayDefinition($row['text'], $row['image']),
                new EmptyDefinition()
            );

            $id += 1;
        }

        return new QuestionAnswerOptionsSetEvent(
            $this->factory->fromString($data['question_id']),
            (new DateTimeImmutable())->setTimestamp($data['occurred_on']),
            $options
        );
    }
}