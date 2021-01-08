<?php
declare(strict_types=1);

namespace srag\asq\Questions\Choice\Storage\MultipleChoice;

use ilDateTime;
use srag\CQRS\Event\DomainEvent;
use srag\asq\Domain\Event\QuestionPlayConfigurationSetEvent;
use srag\asq\Domain\Model\Configuration\QuestionPlayConfiguration;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\AbstractEventStorageHandler;
use srag\asq\Questions\Choice\Editor\MultipleChoice\Data\MultipleChoiceEditorConfiguration;
use srag\asq\Questions\Choice\Scoring\Data\MultipleChoiceScoringConfiguration;

/**
 * Class MultipleChoiceConfigurationSetEventHandler
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class MultipleChoiceConfigurationSetEventHandler extends AbstractEventStorageHandler
{
    /**
     * @param DomainEvent $event
     */
    public function handleEvent(DomainEvent $event, int $event_id) : void
    {
        /** @var $choice_config MultipleChoiceEditorConfiguration */
        $choice_config = $event->getPlayConfiguration()->getEditorConfiguration();

        $choice_id = intval($this->db->nextId(SetupMultipleChoice::TABLENAME_MULTIPLE_CHOICE_CONFIGURATION));
        $this->db->insert(SetupMultipleChoice::TABLENAME_MULTIPLE_CHOICE_CONFIGURATION, [
            'choice_id' => ['integer', $choice_id],
            'event_id' => ['integer', $event_id],
            'shuffle' => ['boolean', $choice_config->isShuffleAnswers()],
            'max_answers' => ['integer', $choice_config->getMaxAnswers()],
            'thumbnail' => ['integer', $choice_config->getThumbnailSize()],
            'singleline' => ['boolean', $choice_config->isSingleLine()]
        ]);
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\Infrastructure\Persistence\RelationalEventStore\AbstractEventStorageHandler::getQueryString()
     */
    public function getQueryString(): string
    {
        return 'select * from ' . SetupMultipleChoice::TABLENAME_MULTIPLE_CHOICE_CONFIGURATION .' where event_id in(%s)';
    }

    public function createEvent(array $data, array $rows): DomainEvent
    {
        return new QuestionPlayConfigurationSetEvent(
            $this->factory->fromString($data['question_id']),
            new ilDateTime($data['occurred_on'], IL_CAL_UNIX),
            $this->readInt($data['initiating_user_id']),
            new QuestionPlayConfiguration(
                new MultipleChoiceEditorConfiguration(
                    $this->readBool($rows[0]['shuffle']),
                    $this->readInt($rows[0]['max_answers']),
                    $this->readInt($rows[0]['thumbnail']),
                    $this->readBool($rows[0]['singleline'])
                ),
                new MultipleChoiceScoringConfiguration()
            )
        );
    }
}