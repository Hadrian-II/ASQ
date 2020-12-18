<?php
declare(strict_types=1);

namespace srag\asq\Questions\Kprim\Storage;

use ilDateTime;
use srag\CQRS\Event\DomainEvent;
use srag\asq\Domain\Event\QuestionAnswerOptionsSetEvent;
use srag\asq\Domain\Model\Answer\Option\AnswerOption;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\AbstractEventStorageHandler;
use srag\asq\Questions\Generic\Data\ImageAndTextDisplayDefinition;
use srag\asq\Questions\Kprim\Scoring\Data\KprimChoiceScoringDefinition;

/**
 * Class KprimAnswerOptionsSetEventHandler
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class KprimAnswerOptionsSetEventHandler extends AbstractEventStorageHandler
{
    /**
     * @param DomainEvent $event
     */
    public function handleEvent(DomainEvent $event, int $event_id) : void
    {
        /** @var $answer_options AnswerOption[] */
        $answer_options = $event->getAnswerOptions();

        foreach ($answer_options as $option) {
            $answer_id = intval($this->db->nextId(SetupKprim::TABLENAME_KPRIM_ANSWER));

            /** @var $scoring_definition KprimChoiceScoringDefinition */
            $scoring_definition = $option->getScoringDefinition();
            /** @var $display_definition ImageAndTextDisplayDefinition */
            $display_definition = $option->getDisplayDefinition();

            $this->db->insert(SetupKprim::TABLENAME_KPRIM_ANSWER, [
                'answer_id' => ['integer', $answer_id],
                'event_id' => ['integer', $event_id],
                'correct_answer' => ['boolean', $scoring_definition->isCorrectValue()],
                'text' => ['text', $display_definition->getText()],
                'image' => ['text', $display_definition->getImage()]
            ]);
        }
    }

    /**
     * @param array $data
     * @return DomainEvent
     */
    public function loadEvent(array $data) : DomainEvent
    {
        $options = [];

        $res = $this->db->query(
            sprintf(
                'select * from ' . SetupKprim::TABLENAME_KPRIM_ANSWER . ' c
                 where c.event_id = %s',
                $this->db->quote($data['id'], 'int')
                )
            );

        $id = 1;
        while($row = $this->db->fetchAssoc($res)) {
            $options[] = new AnswerOption(
                strval($id),
                new ImageAndTextDisplayDefinition(
                    $row['text'],
                    $row['image']
                ),
                new KprimChoiceScoringDefinition(
                    boolval($row['correct_answer'])
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