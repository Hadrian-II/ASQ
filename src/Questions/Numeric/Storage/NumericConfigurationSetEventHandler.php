<?php
declare(strict_types=1);

namespace srag\asq\Questions\Numeric\Storage;

use ilDateTime;
use srag\CQRS\Event\DomainEvent;
use srag\asq\Domain\Event\QuestionPlayConfigurationSetEvent;
use srag\asq\Domain\Model\Configuration\QuestionPlayConfiguration;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\AbstractEventStorageHandler;
use srag\asq\Questions\Numeric\Editor\Data\NumericEditorConfiguration;
use srag\asq\Questions\Numeric\Scoring\Data\NumericScoringConfiguration;

/**
 * Class NumericConfigurationSetEventHandler
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class NumericConfigurationSetEventHandler extends AbstractEventStorageHandler
{
    /**
     * @param DomainEvent $event
     */
    public function handleEvent(DomainEvent $event, int $event_id) : void
    {
        /** @var $editor_config NumericEditorConfiguration */
        $editor_config = $event->getPlayConfiguration()->getEditorConfiguration();
        /** @var $scoring_config NumericScoringConfiguration */
        $scoring_config = $event->getPlayConfiguration()->getScoringConfiguration();

        $id = intval($this->db->nextId(SetupNumeric::TABLENAME_NUMERIC_CONFIGURATION));
        $this->db->insert(SetupNumeric::TABLENAME_NUMERIC_CONFIGURATION, [
            'config_id' => ['integer', $id],
            'event_id' => ['integer', $event_id],
            'points' => ['float', $scoring_config->getPoints()],
            'lower_bound' => ['float', $scoring_config->getLowerBound()],
            'upper_bound' => ['float', $scoring_config->getUpperBound()],
            'max_chars' => ['integer', $editor_config->getMaxNumOfChars()]
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
                'select * from ' . SetupNumeric::TABLENAME_NUMERIC_CONFIGURATION .' c
                 where c.event_id = %s',
                $this->db->quote($data['id'], 'int')
                )
            );

        $item = $this->db->fetchAssoc($res);

        return new QuestionPlayConfigurationSetEvent(
            $this->factory->fromString($data['question_id']),
            new ilDateTime($data['occurred_on'], IL_CAL_UNIX),
            intval($data['initiating_user_id']),
            new QuestionPlayConfiguration(
                new NumericEditorConfiguration(
                        intval($item['max_chars'])
                ),
                new NumericScoringConfiguration(
                    floatval($item['points']),
                    floatval($item['lower_bound']),
                    floatval($item['upper_bound'])
                )
            )
        );
    }
}