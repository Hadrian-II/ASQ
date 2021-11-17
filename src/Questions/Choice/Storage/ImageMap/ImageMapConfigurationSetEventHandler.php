<?php
declare(strict_types=1);

namespace srag\asq\Questions\Choice\Storage\ImageMap;

use DateTimeImmutable;
use Fluxlabs\CQRS\Event\DomainEvent;
use srag\asq\Domain\Event\QuestionPlayConfigurationSetEvent;
use srag\asq\Domain\Model\Configuration\QuestionPlayConfiguration;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\AbstractEventStorageHandler;
use srag\asq\Questions\Choice\Scoring\Data\MultipleChoiceScoringConfiguration;
use srag\asq\Questions\Choice\Editor\ImageMap\Data\ImageMapEditorConfiguration;

/**
 * Class ImageMapConfigurationSetEventHandler
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class ImageMapConfigurationSetEventHandler extends AbstractEventStorageHandler
{
    public function handleEvent(DomainEvent $event, int $event_id) : void
    {
        /** @var $image_config ImageMapEditorConfiguration */
        $image_config = $event->getPlayConfiguration()->getEditorConfiguration();

        $image_id = intval($this->db->nextId(SetupImageMap::TABLENAME_IMAGEMAP_CONFIGURATION));
        $this->db->insert(SetupImageMap::TABLENAME_IMAGEMAP_CONFIGURATION, [
            'image_id' => ['integer', $image_id],
            'event_id' => ['integer', $event_id],
            'image' => ['text', $image_config->getImage()],
            'is_multi' => ['integer', $image_config->isMultipleChoice()],
            'max_answers' => ['integer', $image_config->getMaxAnswers()]
        ]);
    }

    public function getQueryString(): string
    {
        return 'select * from ' . SetupImageMap::TABLENAME_IMAGEMAP_CONFIGURATION .' where event_id in(%s)';
    }

    public function createEvent(array $data, array $rows): DomainEvent
    {
        return new QuestionPlayConfigurationSetEvent(
            $this->factory->fromString($data['question_id']),
            (new DateTimeImmutable())->setTimestamp($data['occurred_on']),
            new QuestionPlayConfiguration(
                new ImageMapEditorConfiguration(
                    $rows[0]['image'],
                    $this->readBool($rows[0]['is_multi']),
                    $this->readInt($rows[0]['max_answers'])
                    ),
                new MultipleChoiceScoringConfiguration()
            )
        );
    }
}