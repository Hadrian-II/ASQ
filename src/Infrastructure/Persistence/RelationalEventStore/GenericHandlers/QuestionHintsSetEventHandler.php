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
     * @param array $data
     * @return DomainEvent
     */
    public function loadEvent(array $data) : DomainEvent
    {
        $res = $this->db->query(
            sprintf(
                'select * from ' . RelationalQuestionEventStore::TABLE_NAME_QUESTION_HINT .' where event_id = %s',
                $this->db->quote($data['id'], 'int')
            )
        );

        $hints = [];
        while ($row = $this->db->fetchAssoc($res))
        {
            $hints[] = QuestionHint::create($row['hint_id'], $row['content'], floatval($row['deduction']));
        }

        return new QuestionHintsSetEvent(
            $this->factory->fromString($data['question_id']),
            new ilDateTime($data['occurred_on'], IL_CAL_UNIX),
            intval($data['initiating_user_id']),
            QuestionHints::create($hints)
       );
    }
}