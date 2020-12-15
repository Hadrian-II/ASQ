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
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class EssayConfigurationSetEventHandler extends AbstractEventStorageHandler
{
    /**
     * @param DomainEvent $event
     */
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

    /**
     * @param array $data
     * @return DomainEvent
     */
    public function loadEvent(array $data) : DomainEvent
    {
        $res = $this->db->query(
            sprintf(
                'select * from ' . SetupEssay::TABLENAME_ESSAY_CONFIGURATION .' c
                 where c.event_id = %s',
                $this->db->quote($data['id'], 'int')
                )
            );

        $row = $this->db->fetchAssoc($res);

        return new QuestionPlayConfigurationSetEvent(
            $this->factory->fromString($data['question_id']),
            new ilDateTime($data['occurred_on'], IL_CAL_UNIX),
            intval($data['initiating_user_id']),
            new QuestionPlayConfiguration(
                new EssayEditorConfiguration(
                        intval($row['max_length'])
                    ),
                new EssayScoringConfiguration(
                        intval($row['matchmode']),
                        intval($row['scoremode']),
                        floatval($row['points'])
                    )
                )
            );
    }
}