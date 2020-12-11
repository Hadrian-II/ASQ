<?php
declare(strict_types=1);

namespace srag\asq\Questions\Choice\Storage\ImageMap;

use ilDateTime;
use srag\CQRS\Event\DomainEvent;
use srag\asq\Domain\Event\QuestionAnswerOptionsSetEvent;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\AbstractEventStorageHandler;
use srag\asq\Domain\Model\Answer\Option\AnswerOption;
use srag\asq\Questions\Choice\Scoring\Data\MultipleChoiceScoringDefinition;
use srag\asq\Questions\Choice\Editor\ImageMap\Data\ImageMapEditorDefinition;

/**
 * Class ImageMapAnswerOptionsSetEventHandler
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class ImageMapAnswerOptionsSetEventHandler extends AbstractEventStorageHandler
{
    /**
     * @param DomainEvent $event
     */
    public function handleEvent(DomainEvent $event, int $event_id) : void
    {
        /** @var $answer_options AnswerOption[] */
        $answer_options = $event->getAnswerOptions();

        foreach ($answer_options as $option) {
            $answer_id = intval($this->db->nextId(SetupImageMap::TABLENAME_IMAGEMAP_ANSWER));
            $this->db->insert(SetupImageMap::TABLENAME_IMAGEMAP_ANSWER, [
                'answer_id' => ['integer', $answer_id],
                'event_id' => ['integer', $event_id],
                'points_selected' => ['float', $option->getScoringDefinition()->getPointsSelected()],
                'points_unselected' => ['float', $option->getScoringDefinition()->getPointsUnSelected()],
                'tooltip' => ['text', $option->getDisplayDefinition()->getTooltip()],
                'type' => ['integer', $option->getDisplayDefinition()->getType()],
                'coordinates' => ['text', $option->getDisplayDefinition()->getCoordinates()]
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
                'select * from ' . SetupImageMap::TABLENAME_IMAGEMAP_ANSWER . ' c
                 where c.event_id = %s',
                $this->db->quote($data['id'], 'int')
                )
            );

        $id = 1;
        while($row = $this->db->fetchAssoc($res)) {
            $options[] = new AnswerOption(
                strval($id),
                new ImageMapEditorDefinition(
                    $row['tooltip'],
                    intval($row['type']),
                    $row['coordinates']),
                new MultipleChoiceScoringDefinition(
                    floatval($row['points_selected']),
                    floatval($row['points_unselected'])
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