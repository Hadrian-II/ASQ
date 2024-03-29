<?php
declare(strict_types=1);

namespace srag\asq\Questions\Choice\Storage\MultipleChoice;

use DateTimeImmutable;
use Fluxlabs\CQRS\Event\DomainEvent;
use srag\asq\Domain\Event\QuestionAnswerOptionsSetEvent;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\AbstractEventStorageHandler;
use srag\asq\Domain\Model\Answer\Option\AnswerOption;
use srag\asq\Questions\Generic\Data\ImageAndTextDisplayDefinition;
use srag\asq\Questions\Choice\Scoring\Data\MultipleChoiceScoringDefinition;

/**
 * Class MultipleChoiceAnswerOptionsSetEventHandler
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class MultipleChoiceAnswerOptionsSetEventHandler extends AbstractEventStorageHandler
{
    public function handleEvent(DomainEvent $event, int $event_id) : void
    {
        /** @var $answer_options AnswerOption[] */
        $answer_options = $event->getAnswerOptions();

        foreach ($answer_options as $option) {
            $answer_id = intval($this->db->nextId(SetupMultipleChoice::TABLENAME_MULTIPLE_CHOICE_ANSWER));
            $this->db->insert(SetupMultipleChoice::TABLENAME_MULTIPLE_CHOICE_ANSWER, [
                'answer_id' => ['integer', $answer_id],
                'event_id' => ['integer', $event_id],
                'points_selected' => ['float', $option->getScoringDefinition()->getPointsSelected()],
                'points_unselected' => ['float', $option->getScoringDefinition()->getPointsUnSelected()],
                'text' => ['clob', $option->getDisplayDefinition()->getText()],
                'image' => ['text', $option->getDisplayDefinition()->getImage()]
            ]);
        }
    }

    public function getQueryString(): string
    {
        return 'select * from ' . SetupMultipleChoice::TABLENAME_MULTIPLE_CHOICE_ANSWER . ' where event_id in(%s)';
    }

    public function createEvent(array $data, array $rows): DomainEvent
    {
        $id = 1;
        $options = [];
        foreach ($rows as $row) {
            $options[] = new AnswerOption(
                strval($id),
                new ImageAndTextDisplayDefinition($row['text'], $row['image']),
                new MultipleChoiceScoringDefinition(
                    $this->readFloat($row['points_selected']),
                    $this->readFloat($row['points_unselected'])
                )
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