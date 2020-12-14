<?php
declare(strict_types=1);

namespace srag\asq\Questions\ErrorText\Storage;

use ilDateTime;
use srag\CQRS\Event\DomainEvent;
use srag\asq\Domain\Event\QuestionPlayConfigurationSetEvent;
use srag\asq\Domain\Model\Configuration\QuestionPlayConfiguration;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\AbstractEventStorageHandler;
use srag\asq\Questions\ErrorText\Editor\Data\ErrorTextEditorConfiguration;
use srag\asq\Questions\ErrorText\Scoring\Data\ErrorTextScoringConfiguration;

/**
 * Class ErrorTextConfigurationSetEventHandler
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class ErrorTextConfigurationSetEventHandler extends AbstractEventStorageHandler
{
    /**
     * @param DomainEvent $event
     */
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

    /**
     * @param array $data
     * @return DomainEvent
     */
    public function loadEvent(array $data) : DomainEvent
    {
        $res = $this->db->query(
            sprintf(
                'select * from ' . SetupErrorText::TABLENAME_ERRORTEXT_CONFIGURATION .' c
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
                new ErrorTextEditorConfiguration(
                        $row['errortext'],
                        intval($row['txt_size'])
                    ),
                new ErrorTextScoringConfiguration(
                        floatval($row['points_wrong'])
                    )
                )
            );
    }
}