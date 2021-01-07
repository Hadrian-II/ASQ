<?php
declare(strict_types=1);

namespace srag\asq\Questions\TextSubset\Storage;

use ilDateTime;
use srag\CQRS\Event\DomainEvent;
use srag\asq\Domain\Event\QuestionAnswerOptionsSetEvent;
use srag\asq\Domain\Model\Answer\Option\AnswerOption;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\AbstractEventStorageHandler;
use srag\asq\Questions\Generic\Data\EmptyDefinition;
use srag\asq\Questions\TextSubset\Scoring\Data\TextSubsetScoringDefinition;

/**
 * Class TextSubsetAnswerOptionsSetEventHandler
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class TextSubsetAnswerOptionsSetEventHandler extends AbstractEventStorageHandler
{
    /**
     * @param DomainEvent $event
     */
    public function handleEvent(DomainEvent $event, int $event_id) : void
    {
        /** @var $answer_options AnswerOption[] */
        $answer_options = $event->getAnswerOptions();

        foreach ($answer_options as $option) {
            $answer_id = intval($this->db->nextId(SetupTextSubset::TABLENAME_TEXT_SUBSET_ANSWER));
            $this->db->insert(SetupTextSubset::TABLENAME_TEXT_SUBSET_ANSWER, [
                'answer_id' => ['integer', $answer_id],
                'event_id' => ['integer', $event_id],
                'text' => ['clob', $option->getScoringDefinition()->getText()],
                'points' => ['text', $option->getScoringDefinition()->getPoints()]
            ]);
        }
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\Infrastructure\Persistence\RelationalEventStore\AbstractEventStorageHandler::getQueryString()
     */
    public function getQueryString(): string
    {
        return 'select * from ' . SetupTextSubset::TABLENAME_TEXT_SUBSET_ANSWER . ' where event_id in(%s)';
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\Infrastructure\Persistence\RelationalEventStore\AbstractEventStorageHandler::createEvent()
     */
    public function createEvent(array $data, array $rows): DomainEvent
    {
        $id = 1;
        foreach ($rows as $row) {
            $options[] = new AnswerOption(
                strval($id),
                new EmptyDefinition(),
                new TextSubsetScoringDefinition(floatval($row['points']), $row['text'])
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