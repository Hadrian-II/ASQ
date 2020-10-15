<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Persistence\RelationalEventStore\GenericHandlers;

use srag\CQRS\Event\DomainEvent;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\IEventStorageHandler;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\RelationalQuestionEventStore;
use srag\asq\Domain\Model\Hint\QuestionHint;
use srag\asq\Domain\Event\QuestionHintsSetEvent;
use srag\asq\Domain\Model\Hint\QuestionHints;
use ilDateTime;

/**
 * Class QuestionHintsSetEventHandler
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionHintsSetEventHandler implements IEventStorageHandler
{
    /**
     * @param DomainEvent $event
     */
    public function handleEvent(DomainEvent $event, int $event_id) : void
    {
        $hints = $event->getHints()->getHints();

        foreach ($hints as $hint) {
            $this->db->insert(RelationalQuestionEventStore::TABLE_NAME_QUESTION_HINT, [
                'event_id' => $event_id,
                'hint_id' => $hint->getId(),
                'content' => $hint->getContent(),
                'deduction' => $hint->getPointDeduction()
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
                $this->db->quote($data['event_id'], 'int')
            )
        );

        $hints = [];
        while ($row = $this->db->fetchAssoc($res))
        {
            $hints[] = QuestionHint::create($row['hint_id'], $row['content'], $row['deduction']);
        }

        return new QuestionHintsSetEvent(
            $this->factory->fromString($data['question_id']),
            new ilDateTime($data['occurred_on'], IL_CAL_UNIX),
            $data['initiating_user_id'],
            QuestionHints::create($hints)
       );
    }
}