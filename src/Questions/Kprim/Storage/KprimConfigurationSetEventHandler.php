<?php
declare(strict_types=1);

namespace srag\asq\Questions\Kprim\Storage;

use ilDateTime;
use srag\CQRS\Event\DomainEvent;
use srag\asq\Domain\Event\QuestionPlayConfigurationSetEvent;
use srag\asq\Domain\Model\Configuration\QuestionPlayConfiguration;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\AbstractEventStorageHandler;
use srag\asq\Questions\Kprim\Editor\Data\KprimChoiceEditorConfiguration;
use srag\asq\Questions\Kprim\Scoring\Data\KprimChoiceScoringConfiguration;

/**
 * Class KprimConfigurationSetEventHandler
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class KprimConfigurationSetEventHandler extends AbstractEventStorageHandler
{
    public function handleEvent(DomainEvent $event, int $event_id) : void
    {
        /** @var $editor_config KprimChoiceEditorConfiguration */
        $editor_config = $event->getPlayConfiguration()->getEditorConfiguration();
        /** @var $scoring_config KprimChoiceScoringConfiguration */
        $scoring_config = $event->getPlayConfiguration()->getScoringConfiguration();

        $id = intval($this->db->nextId(SetupKprim::TABLENAME_KPRIM_CONFIGURATION));
        $this->db->insert(SetupKprim::TABLENAME_KPRIM_CONFIGURATION, [
            'config_id' => ['integer', $id],
            'event_id' => ['integer', $event_id],
            'shuffle' => ['integer', $editor_config->isShuffleAnswers()],
            'thumbnail_size' => ['integer', $editor_config->getThumbnailSize()],
            'label_true' => ['text', $editor_config->getLabelTrue()],
            'label_false' => ['text', $editor_config->getLabelFalse()],
            'points' => ['float', $scoring_config->getPoints()],
            'half_points_at' => ['integer', $scoring_config->getHalfPointsAt()]
        ]);
    }

    public function getQueryString(): string
    {
        return 'select * from ' . SetupKprim::TABLENAME_KPRIM_CONFIGURATION .' where event_id in(%s)';
    }

    public function createEvent(array $data, array $rows): DomainEvent
    {
        $row = $rows[0];

        return new QuestionPlayConfigurationSetEvent(
            $this->factory->fromString($data['question_id']),
            new ilDateTime($data['occurred_on'], IL_CAL_UNIX),
            $this->readInt($data['initiating_user_id']),
            new QuestionPlayConfiguration(
                new KprimChoiceEditorConfiguration(
                    $this->readBool($row['shuffle']),
                    $this->readInt($row['thumbnail_size']),
                    $row['label_true'],
                    $row['label_false']
                ),
                new KprimChoiceScoringConfiguration(
                    $this->readFloat($row['points']),
                    $this->readInt($row['half_points_at'])
                )
            )
        );
    }
}