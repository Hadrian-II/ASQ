<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Persistence\RelationalEventStore\GenericHandlers;

use srag\CQRS\Event\DomainEvent;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\AbstractEventStorageHandler;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\RelationalQuestionEventStore;
use srag\asq\Domain\Model\Feedback\Feedback;
use srag\asq\Domain\Event\QuestionFeedbackSetEvent;
use ilDateTime;

/**
 * Class QuestionFeedbackSetEventHandler
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionFeedbackSetEventHandler extends  AbstractEventStorageHandler
{
    /**
     * @param DomainEvent $event
     */
    public function handleEvent(DomainEvent $event, int $event_id) : void
    {
        /** @var $feedback Feedback */
        $feedback = $event->getFeedback();

        $feedback_id = $this->db->nextId(RelationalQuestionEventStore::TABLE_NAME_QUESTION_FEEDBACK);
        $this->db->insert(RelationalQuestionEventStore::TABLE_NAME_QUESTION_FEEDBACK, [
            'id' => ['integer' => $feedback_id],
            'event_id' => ['integer', $event_id],
            'feedback_correct' => ['clob', $feedback->getAnswerCorrectFeedback()],
            'feedback_wrong' => ['clob', $feedback->getAnswerWrongFeedback()],
            'answer_feedback_type' => ['integer', $feedback->getAnswerOptionFeedbackMode()]
        ]);

        foreach ($feedback->getAnswerOptionFeedbacks() as $answer_id => $content) {
            $this->db->insert(RelationalQuestionEventStore::TABLE_NAME_QUESTION_ANSWER_FEEDBACK, [
                'feedback_id' => ['integer', $feedback_id],
                'answer_id' => ['text', $answer_id],
                'content' => ['clob', $content]
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
                'select * from ' . RelationalQuestionEventStore::TABLE_NAME_QUESTION_FEEDBACK .' as f
                 inner join' . RelationalQuestionEventStore::TABLE_NAME_QUESTION_ANSWER_FEEDBACK .' as af on f.feedback_id = af.feedback_id
                 where f.event_id = %s',
                $this->db->quote($data['id'], 'int')
                )
            );

        $answer_feedback = [];
        while ($row = $this->db->fetchAssoc($res))
        {
            $answer_feedback[$row['af.answer_id']] = $row['af.content'];
        }

        return new QuestionFeedbackSetEvent(
            $this->factory->fromString($data['question_id']),
            new ilDateTime($data['occurred_on'], IL_CAL_UNIX),
            intval($data['initiating_user_id']),
            Feedback::create(
                $row['f.feedback_correct'],
                $row['f.feedback_wrong'],
                intval($row['f.answer_feedback_type']),
                $answer_feedback
            )
        );
    }
}