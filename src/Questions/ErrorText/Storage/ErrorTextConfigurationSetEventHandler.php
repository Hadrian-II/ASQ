<?php
declare(strict_types=1);

namespace srag\asq\Questions\ErrorText\Storage;

use ilDateTime;
use Fluxlabs\CQRS\Event\DomainEvent;
use srag\asq\Domain\Event\QuestionPlayConfigurationSetEvent;
use srag\asq\Domain\Model\Configuration\QuestionPlayConfiguration;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\AbstractEventStorageHandler;
use srag\asq\Questions\ErrorText\Editor\Data\ErrorTextEditorConfiguration;
use srag\asq\Questions\ErrorText\Scoring\Data\ErrorTextScoringConfiguration;

/**
 * Class ErrorTextConfigurationSetEventHandler
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class ErrorTextConfigurationSetEventHandler extends AbstractEventStorageHandler
{
    public function handleEvent(DomainEvent $event, int $event_id) : void
    {
        /** @var $error_editor_config ErrorTextEditorConfiguration */
        $error_editor_config = $event->getPlayConfiguration()->getEditorConfiguration();
        /** @var $error_scoring_config ErrorTextScoringConfiguration */
        $error_scoring_config = $event->getPlayConfiguration()->getScoringConfiguration();

        $errtxt_id = intval($this->db->nextId(SetupErrorText::TABLENAME_ERRORTEXT_CONFIGURATION));
        $this->db->insert(SetupErrorText::TABLENAME_ERRORTEXT_CONFIGURATION, [
            'errtxt_id' => ['integer', $errtxt_id],
            'event_id' => ['integer', $event_id],
            'errortext' => ['text', $error_editor_config->getErrorText()],
            'txt_size' => ['integer', $error_editor_config->getTextSize()],
            'points_wrong' => ['float', $error_scoring_config->getPointsWrong()]
        ]);
    }

    public function getQueryString(): string
    {
        return 'select * from ' . SetupErrorText::TABLENAME_ERRORTEXT_CONFIGURATION .' where event_id in(%s)';
    }

    public function createEvent(array $data, array $rows): DomainEvent
    {
        return new QuestionPlayConfigurationSetEvent(
            $this->factory->fromString($data['question_id']),
            new ilDateTime($data['occurred_on'], IL_CAL_UNIX),
            $this->readInt($data['initiating_user_id']),
            new QuestionPlayConfiguration(
                new ErrorTextEditorConfiguration(
                    $rows[0]['errortext'],
                    $this->readInt($rows[0]['txt_size'])
                ),
                new ErrorTextScoringConfiguration(
                    $this->readFloat($rows[0]['points_wrong'])
                )
            )
        );
    }
}