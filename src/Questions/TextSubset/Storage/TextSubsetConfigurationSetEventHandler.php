<?php
declare(strict_types=1);

namespace srag\asq\Questions\TextSubset\Storage;

use DateTimeImmutable;
use Fluxlabs\CQRS\Event\DomainEvent;
use srag\asq\Domain\Event\QuestionPlayConfigurationSetEvent;
use srag\asq\Domain\Model\Configuration\QuestionPlayConfiguration;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\AbstractEventStorageHandler;
use srag\asq\Questions\TextSubset\Editor\Data\TextSubsetEditorConfiguration;
use srag\asq\Questions\TextSubset\Scoring\Data\TextSubsetScoringConfiguration;

/**
 * Class TextSubsetConfigurationSetEventHandler
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class TextSubsetConfigurationSetEventHandler extends AbstractEventStorageHandler
{
    public function handleEvent(DomainEvent $event, int $event_id) : void
    {
        /** @var $editor_config TextSubsetEditorConfiguration */
        $editor_config = $event->getPlayConfiguration()->getEditorConfiguration();
        /** @var $scoring_config TextSubsetScoringConfiguration */
        $scoring_config = $event->getPlayConfiguration()->getScoringConfiguration();

        $id = intval($this->db->nextId(SetupTextSubset::TABLENAME_TEXT_SUBSET_CONFIGURATION));
        $this->db->insert(SetupTextSubset::TABLENAME_TEXT_SUBSET_CONFIGURATION, [
            'config_id' => ['integer', $id],
            'event_id' => ['integer', $event_id],
            'answers' => ['integer', $editor_config->getNumberOfRequestedAnswers()],
            'matching' => ['integer', $scoring_config->getTextMatching()]
        ]);
    }

    public function getQueryString(): string
    {
        return 'select * from ' . SetupTextSubset::TABLENAME_TEXT_SUBSET_CONFIGURATION .' where event_id in(%s)';
    }

    public function createEvent(array $data, array $rows): DomainEvent
    {
        $item = $rows[0];

        return new QuestionPlayConfigurationSetEvent(
            $this->factory->fromString($data['question_id']),
            (new DateTimeImmutable())->setTimestamp($data['occurred_on']),
            new QuestionPlayConfiguration(
                new TextSubsetEditorConfiguration(
                    $this->readInt($item['answers'])
                ),
                new TextSubsetScoringConfiguration(
                    $this->readInt($item['matching'])
                )
            )
        );
    }
}