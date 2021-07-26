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

    public function getQueryString(): string
    {
        return 'select * from ' . SetupNumeric::TABLENAME_NUMERIC_CONFIGURATION .' where event_id in(%s)';
    }

    public function createEvent(array $data, array $rows): DomainEvent
    {
        $item = $rows[0];

        return new QuestionPlayConfigurationSetEvent(
            $this->factory->fromString($data['question_id']),
            new ilDateTime($data['occurred_on'], IL_CAL_UNIX),
            intval($data['initiating_user_id']),
            new QuestionPlayConfiguration(
                new NumericEditorConfiguration(
                    $this->readInt($item['max_chars'])
                ),
                new NumericScoringConfiguration(
                    $this->readFloat($item['points']),
                    $this->readFloat($item['lower_bound']),
                    $this->readFloat($item['upper_bound'])
                )
            )
        );
    }
}