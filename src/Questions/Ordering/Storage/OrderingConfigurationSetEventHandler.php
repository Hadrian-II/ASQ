<?php
declare(strict_types=1);

namespace srag\asq\Questions\Ordering\Storage;

use ilDateTime;
use srag\CQRS\Event\DomainEvent;
use srag\asq\Domain\Event\QuestionPlayConfigurationSetEvent;
use srag\asq\Domain\Model\Configuration\QuestionPlayConfiguration;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\AbstractEventStorageHandler;
use srag\asq\Questions\Ordering\Editor\Data\OrderingEditorConfiguration;
use srag\asq\Questions\Ordering\Scoring\Data\OrderingScoringConfiguration;

/**
 * Class OrderingConfigurationSetEventHandler
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class OrderingConfigurationSetEventHandler extends AbstractEventStorageHandler
{
    /**
     * @param DomainEvent $event
     */
    public function handleEvent(DomainEvent $event, int $event_id) : void
    {
        /** @var $editor_config OrderingEditorConfiguration */
        $editor_config = $event->getPlayConfiguration()->getEditorConfiguration();
        /** @var $scoring_config OrderingScoringConfiguration */
        $scoring_config = $event->getPlayConfiguration()->getScoringConfiguration();

        $id = intval($this->db->nextId(SetupOrdering::TABLENAME_ORDERING_CONFIGURATION));
        $this->db->insert(SetupOrdering::TABLENAME_ORDERING_CONFIGURATION, [
            'config_id' => ['integer', $id],
            'event_id' => ['integer', $event_id],
            'text' => ['text', $editor_config->getText()],
            'is_vertical' => [ 'integer', $editor_config->isVertical()],
            'points' => ['float', $scoring_config->getPoints()]
        ]);
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\Infrastructure\Persistence\RelationalEventStore\AbstractEventStorageHandler::getQueryString()
     */
    public function getQueryString(): string
    {
        return 'select * from ' . SetupOrdering::TABLENAME_ORDERING_CONFIGURATION .' where event_id = %s';
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\Infrastructure\Persistence\RelationalEventStore\AbstractEventStorageHandler::createEvent()
     */
    public function createEvent(array $data, array $rows): DomainEvent
    {
        $item = $rows[0];

        return new QuestionPlayConfigurationSetEvent(
            $this->factory->fromString($data['question_id']),
            new ilDateTime($data['occurred_on'], IL_CAL_UNIX),
            intval($data['initiating_user_id']),
            new QuestionPlayConfiguration(
                new OrderingEditorConfiguration(
                    boolval($item['is_vertical']),
                    $item['text']
                ),
                new OrderingScoringConfiguration(
                    floatval($item['points'])
                )
            )
        );
    }
}