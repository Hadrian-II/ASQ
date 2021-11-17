<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Persistence\RelationalEventStore\GenericHandlers;

use DateTimeImmutable;
use Fluxlabs\CQRS\Event\DomainEvent;
use srag\asq\Domain\Event\QuestionHintsSetEvent;
use srag\asq\Domain\Model\Hint\QuestionHint;
use srag\asq\Domain\Model\Hint\QuestionHints;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\AbstractEventStorageHandler;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\RelationalQuestionEventStore;

/**
 * Class QuestionHintsSetEventHandler
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class QuestionHintsSetEventHandler extends AbstractEventStorageHandler
{
    public function handleEvent(DomainEvent $event, int $event_id) : void
    {
        $hints = $event->getHints()->getHints();

        foreach ($hints as $hint) {
            $id = $this->db->nextId(RelationalQuestionEventStore::TABLE_NAME_QUESTION_HINT);
            $this->db->insert(RelationalQuestionEventStore::TABLE_NAME_QUESTION_HINT, [
                'id' => ['integer', $id],
                'event_id' => ['integer', $event_id],
                'hint_id' => ['text', $hint->getId()],
                'content' => ['clob', $hint->getContent()],
                'deduction' => ['float', $hint->getPointDeduction()]
            ]);
        }
    }

    public function getQueryString() : string
    {
        return 'select * from ' . RelationalQuestionEventStore::TABLE_NAME_QUESTION_HINT .' where event_id in(%s)';
    }

    public function createEvent(array $data, array $rows) : DomainEvent
    {
        $hints = [];
        foreach ($rows as $row)
        {
            $hints[] = new QuestionHint($row['hint_id'], $row['content'], floatval($row['deduction']));
        }

        return new QuestionHintsSetEvent(
            $this->factory->fromString($data['question_id']),
            (new DateTimeImmutable())->setTimestamp($data['occurred_on']),
            new QuestionHints($hints)
        );
    }
}