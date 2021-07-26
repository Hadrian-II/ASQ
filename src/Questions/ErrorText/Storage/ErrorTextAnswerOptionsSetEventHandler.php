<?php
declare(strict_types=1);

namespace srag\asq\Questions\ErrorText\Storage;

use ilDateTime;
use srag\CQRS\Event\DomainEvent;
use srag\asq\Domain\Event\QuestionAnswerOptionsSetEvent;
use srag\asq\Domain\Model\Answer\Option\AnswerOption;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\AbstractEventStorageHandler;
use srag\asq\Questions\ErrorText\Scoring\Data\ErrorTextScoringDefinition;
use srag\asq\Questions\Generic\Data\EmptyDefinition;

/**
 * Class ErrorTextAnswerOptionsSetEventHandler
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class ErrorTextAnswerOptionsSetEventHandler extends AbstractEventStorageHandler
{
    public function handleEvent(DomainEvent $event, int $event_id) : void
    {
        /** @var $answer_options AnswerOption[] */
        $answer_options = $event->getAnswerOptions();

        foreach ($answer_options as $option) {
            $answer_id = intval($this->db->nextId(SetupErrorText::TABLENAME_ERRORTEXT_ANSWER));
            /** @var $definition ErrorTextScoringDefinition */
            $definition = $option->getScoringDefinition();
            $this->db->insert(SetupErrorText::TABLENAME_ERRORTEXT_ANSWER, [
                'answer_id' => ['integer', $answer_id],
                'event_id' => ['integer', $event_id],
                'wrong_index' => ['integer', $definition->getWrongWordIndex()],
                'wrong_length' => ['integer', $definition->getWrongWordLength()],
                'correct_text' => ['text', $definition->getCorrectText()],
                'points' => ['float', $definition->getPoints()]
            ]);
        }
    }

    public function getQueryString(): string
    {
        return 'select * from ' . SetupErrorText::TABLENAME_ERRORTEXT_ANSWER . ' where event_id in(%s)';
    }

    public function createEvent(array $data, array $rows): DomainEvent
    {
        $id = 1;
        foreach($rows as $row) {
            $options[] = new AnswerOption(
                strval($id),
                new EmptyDefinition(),
                new ErrorTextScoringDefinition(
                    $this->readInt($row['wrong_index']),
                    $this->readInt($row['wrong_length']),
                    $row['correct_text'],
                    $this->readFloat($row['points'])
                    )
                );
            $id += 1;
        }

        return new QuestionAnswerOptionsSetEvent(
            $this->factory->fromString($data['question_id']),
            new ilDateTime($data['occurred_on'], IL_CAL_UNIX),
            intval($data['initiating_user_id']),
            $options
        );
    }
}