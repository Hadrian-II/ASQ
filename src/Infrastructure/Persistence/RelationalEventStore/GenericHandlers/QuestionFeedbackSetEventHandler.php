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
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class QuestionFeedbackSetEventHandler extends  AbstractEventStorageHandler
{
    public function handleEvent(DomainEvent $event, int $event_id) : void
    {
        /** @var $feedback Feedback */
        $feedback = $event->getFeedback();

        $feedback_id = $this->db->nextId(RelationalQuestionEventStore::TABLE_NAME_QUESTION_FEEDBACK);
        $this->db->insert(RelationalQuestionEventStore::TABLE_NAME_QUESTION_FEEDBACK, [
            'id' => ['integer', $feedback_id],
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

    public function getQueryString() : string
    {
        return   'select * from ' . RelationalQuestionEventStore::TABLE_NAME_QUESTION_FEEDBACK .' f
                  left join ' . RelationalQuestionEventStore::TABLE_NAME_QUESTION_ANSWER_FEEDBACK .' af on f.id = af.feedback_id
                  where f.event_id in(%s)';
    }

    public function createEvent(array $data, array $rows) : DomainEvent
    {
        $answer_feedback = [];

        foreach ($rows as $row)
        {
            $answer_feedback[$row['answer_id']] = $row['content'];
        }

        return new QuestionFeedbackSetEvent(
            $this->factory->fromString($data['question_id']),
            new ilDateTime($data['occurred_on'], IL_CAL_UNIX),
            intval($data['initiating_user_id']),
            new Feedback(
                $rows[0]['feedback_correct'],
                $rows[0]['feedback_wrong'],
                intval($rows[0]['answer_feedback_type']),
                $answer_feedback
            )
        );
    }
}