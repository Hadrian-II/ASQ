<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Persistence\RelationalEventStore\GenericHandlers;

use ilDateTime;
use srag\CQRS\Event\DomainEvent;
use srag\asq\Domain\Event\QuestionHintsSetEvent;
use srag\asq\Domain\Model\Hint\QuestionHint;
use srag\asq\Domain\Model\Hint\QuestionHints;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\AbstractEventStorageHandler;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\RelationalQuestionEventStore;

/**
 * Class QuestionHintsSetEventHandler
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionHintsSetEventHandler extends AbstractEventStorageHandler
{
    /**
     * @param DomainEvent $event
     */
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

    /**
     * {@inheritDoc}
     * @see \srag\asq\Infrastructure\Persistence\RelationalEventStore\AbstractEventStorageHandler::getQueryString()
     */
    public function getQueryString(): string
    {
        return 'select * from ' . RelationalQuestionEventStore::TABLE_NAME_QUESTION_HINT .' where event_id in(%s)';
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\Infrastructure\Persistence\RelationalEventStore\AbstractEventStorageHandler::createEvent()
     */
    public function createEvent(array $data, array $rows): DomainEvent
    {
        $hints = [];
        foreach ($rows as $row)
        {
            $hints[] = new QuestionHint($row['hint_id'], $row['content'], floatval($row['deduction']));
        }

        return new QuestionHintsSetEvent(
            $this->factory->fromString($data['question_id']),
            new ilDateTime($data['occurred_on'], IL_CAL_UNIX),
            intval($data['initiating_user_id']),
            new QuestionHints($hints)
        );
    }
}