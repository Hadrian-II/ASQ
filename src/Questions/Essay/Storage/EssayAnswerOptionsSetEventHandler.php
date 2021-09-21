<?php
declare(strict_types=1);

namespace srag\asq\Questions\Essay\Storage;

use ilDateTime;
use Fluxlabs\CQRS\Event\DomainEvent;
use srag\asq\Domain\Event\QuestionAnswerOptionsSetEvent;
use srag\asq\Domain\Model\Answer\Option\AnswerOption;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\AbstractEventStorageHandler;
use srag\asq\Questions\Generic\Data\EmptyDefinition;
use srag\asq\Questions\Essay\Scoring\Data\EssayScoringDefinition;

/**
 * Class EssayAnswerOptionsSetEventHandler
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class EssayAnswerOptionsSetEventHandler extends AbstractEventStorageHandler
{
    public function handleEvent(DomainEvent $event, int $event_id) : void
    {
        /** @var $answer_options AnswerOption[] */
        $answer_options = $event->getAnswerOptions();

        foreach ($answer_options as $option) {
            $answer_id = intval($this->db->nextId(SetupEssay::TABLENAME_ESSAY_ANSWER));
            /** @var $definition EssayScoringDefinition */
            $definition = $option->getScoringDefinition();
            $this->db->insert(SetupEssay::TABLENAME_ESSAY_ANSWER, [
                'answer_id' => ['integer', $answer_id],
                'event_id' => ['integer', $event_id],
                'text' => ['string', $definition->getText()],
                'points' => ['float', $definition->getPoints()]
            ]);
        }
    }

    public function getQueryString(): string
    {
        return 'select * from ' . SetupEssay::TABLENAME_ESSAY_ANSWER . ' where event_id in(%s)';
    }

    public function createEvent(array $data, array $rows): DomainEvent
    {
        $id = 1;
        foreach ($rows as $row) {
            $options[] = new AnswerOption(
                strval($id),
                new EmptyDefinition(),
                new EssayScoringDefinition(
                    $row['text'],
                    $this->readFloat($row['points'])
                    )
                );
            $id += 1;
        }

        return new QuestionAnswerOptionsSetEvent(
            $this->factory->fromString($data['question_id']),
            new ilDateTime($data['occurred_on'], IL_CAL_UNIX),
            $this->readInt($data['initiating_user_id']),
            $options
        );
    }
}