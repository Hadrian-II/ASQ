<?php
declare(strict_types=1);

namespace srag\asq\Questions\Choice\Storage\ImageMap;

use ilDateTime;
use Fluxlabs\CQRS\Event\DomainEvent;
use srag\asq\Domain\Event\QuestionAnswerOptionsSetEvent;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\AbstractEventStorageHandler;
use srag\asq\Domain\Model\Answer\Option\AnswerOption;
use srag\asq\Questions\Choice\Scoring\Data\MultipleChoiceScoringDefinition;
use srag\asq\Questions\Choice\Editor\ImageMap\Data\ImageMapEditorDefinition;

/**
 * Class ImageMapAnswerOptionsSetEventHandler
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class ImageMapAnswerOptionsSetEventHandler extends AbstractEventStorageHandler
{
    public function handleEvent(DomainEvent $event, int $event_id) : void
    {
        /** @var $answer_options AnswerOption[] */
        $answer_options = $event->getAnswerOptions();

        if (is_null($answer_options)) {
            return;
        }

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

    public function getQueryString(): string
    {
        return 'select * from ' . SetupImageMap::TABLENAME_IMAGEMAP_ANSWER . ' where event_id in(%s)';
    }

    public function createEvent(array $data, array $rows): DomainEvent
    {
        $id = 1;
        foreach($rows as $row) {
            $options[] = new AnswerOption(
                strval($id),
                new ImageMapEditorDefinition(
                    $row['tooltip'],
                    $this->readInt($row['type']),
                    $row['coordinates']
                ),
                new MultipleChoiceScoringDefinition(
                    $this->readFloat($row['points_selected']),
                    $this->readFloat($row['points_unselected'])
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