<?php
declare(strict_types=1);

namespace srag\asq\Questions\Formula\Storage;

use ilDateTime;
use srag\CQRS\Event\DomainEvent;
use srag\asq\Domain\Event\QuestionAnswerOptionsSetEvent;
use srag\asq\Domain\Model\Answer\Option\AnswerOption;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\AbstractEventStorageHandler;
use srag\asq\Questions\Generic\Data\EmptyDefinition;
use srag\asq\Questions\Formula\Scoring\Data\FormulaScoringDefinition;

/**
 * Class FormulaAnswerOptionsSetEventHandler
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class FormulaAnswerOptionsSetEventHandler extends AbstractEventStorageHandler
{
    public function handleEvent(DomainEvent $event, int $event_id) : void
    {
        /** @var $answer_options AnswerOption[] */
        $answer_options = $event->getAnswerOptions();

        foreach ($answer_options as $option) {
            $answer_id = intval($this->db->nextId(SetupFormula::TABLENAME_FORMULA_ANSWER));
            /** @var $scoring_definition FormulaScoringDefinition */
            $scoring_definition = $option->getScoringDefinition();

            $this->db->insert(SetupFormula::TABLENAME_FORMULA_ANSWER, [
                'answer_id' => ['integer', $answer_id],
                'event_id' => ['integer', $event_id],
                'formula' => ['text', $scoring_definition->getFormula()],
                'unit' => ['text', $scoring_definition->getUnit()],
                'points' => ['float', $scoring_definition->getPoints()],
            ]);
        }
    }

    public function getQueryString(): string
    {
        return 'select * from ' . SetupFormula::TABLENAME_FORMULA_ANSWER . ' where event_id in(%s)';
    }

    public function createEvent(array $data, array $rows): DomainEvent
    {
        $id = 1;
        foreach ($rows as $row) {
            $options[] = new AnswerOption(
                strval($id),
                new EmptyDefinition(),
                new FormulaScoringDefinition(
                    $row['formula'],
                    $row['unit'],
                    $this->readFloat($row['points'])
                    )
                );
            $id += 1;
        }

        return new QuestionAnswerOptionsSetEvent(
            $this->factory->fromString($data['question_id']),
            new ilDateTime($data['occurred_on'], IL_CAL_UNIX),
            $this->readInt($data['initiating_user_id']),
            $options
        );
    }
}