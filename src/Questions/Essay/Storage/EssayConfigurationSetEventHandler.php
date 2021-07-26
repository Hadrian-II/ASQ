<?php
declare(strict_types=1);

namespace srag\asq\Questions\Essay\Storage;

use ilDateTime;
use srag\CQRS\Event\DomainEvent;
use srag\asq\Domain\Event\QuestionPlayConfigurationSetEvent;
use srag\asq\Domain\Model\Configuration\QuestionPlayConfiguration;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\AbstractEventStorageHandler;
use srag\asq\Questions\Essay\Editor\Data\EssayEditorConfiguration;
use srag\asq\Questions\Essay\Scoring\Data\EssayScoringConfiguration;

/**
 * Class EssayConfigurationSetEventHandler
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class EssayConfigurationSetEventHandler extends AbstractEventStorageHandler
{
    public function handleEvent(DomainEvent $event, int $event_id) : void
    {
        /** @var $editor_config EssayEditorConfiguration */
        $editor_config = $event->getPlayConfiguration()->getEditorConfiguration();
        /** @var $scoring_config EssayScoringConfiguration */
        $scoring_config = $event->getPlayConfiguration()->getScoringConfiguration();

        $id = intval($this->db->nextId(SetupEssay::TABLENAME_ESSAY_CONFIGURATION));
        $this->db->insert(SetupEssay::TABLENAME_ESSAY_CONFIGURATION, [
            'essay_id' => ['integer', $id],
            'event_id' => ['integer', $event_id],
            'matchmode' => ['integer', $scoring_config->getMatchingMode()],
            'scoremode' => ['integer', $scoring_config->getScoringMode()],
            'points' => ['float', $scoring_config->getPoints()],
            'max_length' => ['integer', $editor_config->getMaxLength()]
        ]);
    }

    public function getQueryString(): string
    {
        return 'select * from ' . SetupEssay::TABLENAME_ESSAY_CONFIGURATION .' where event_id in(%s)';
    }

    public function createEvent(array $data, array $rows): DomainEvent
    {
        return new QuestionPlayConfigurationSetEvent(
            $this->factory->fromString($data['question_id']),
            new ilDateTime($data['occurred_on'], IL_CAL_UNIX),
            $this->readInt($data['initiating_user_id']),
            new QuestionPlayConfiguration(
                new EssayEditorConfiguration(
                    $this->readInt($rows[0]['max_length'])
                ),
                new EssayScoringConfiguration(
                    $this->readInt($rows[0]['matchmode']),
                    $this->readInt($rows[0]['scoremode']),
                    $this->readFloat($rows[0]['points'])
                )
            )
        );
    }
}