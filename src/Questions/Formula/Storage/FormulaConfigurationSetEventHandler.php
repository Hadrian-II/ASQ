<?php
declare(strict_types=1);

namespace srag\asq\Questions\Formula\Storage;

use ilDateTime;
use Fluxlabs\CQRS\Event\DomainEvent;
use srag\asq\Domain\Event\QuestionPlayConfigurationSetEvent;
use srag\asq\Domain\Model\Configuration\QuestionPlayConfiguration;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\AbstractEventStorageHandler;
use srag\asq\Questions\Formula\Scoring\Data\FormulaScoringConfiguration;
use srag\asq\Questions\Formula\Editor\Data\FormulaEditorConfiguration;
use srag\asq\Questions\Formula\Scoring\Data\FormulaScoringVariable;

/**
 * Class FormulaConfigurationSetEventHandler
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class FormulaConfigurationSetEventHandler extends AbstractEventStorageHandler
{
    public function handleEvent(DomainEvent $event, int $event_id) : void
    {
        /** @var $scoring_config FormulaScoringConfiguration */
        $scoring_config = $event->getPlayConfiguration()->getScoringConfiguration();

        $id = intval($this->db->nextId(SetupFormula::TABLENAME_FORMULA_CONFIGURATION));
        $this->db->insert(SetupFormula::TABLENAME_FORMULA_CONFIGURATION, [
            'config_id' => ['integer', $id],
            'event_id' => ['integer', $event_id],
            'formula' => ['text', $scoring_config->getFormula()],
            'units' => ['text', $scoring_config->getUnitString()],
            'precision' => ['integer', $scoring_config->getPrecision()],
            'tolerance' => [ 'float', $scoring_config->getTolerance()],
            'result_type' => ['integer', $scoring_config->getResultType()]
        ]);

        foreach ($scoring_config->getVariables() as $var) {
            $this->saveVariable($id, $var);
        }
    }

    private function saveVariable(int $id, FormulaScoringVariable $var) : void
    {
        $this->db->insert(SetupFormula::TABLENAME_FORMULA_VARIABLE, [
            'config_id' => ['integer', $id],
            'min' => ['float', $var->getMin()],
            'max' => ['float', $var->getMax()],
            'unit' => ['text', $var->getUnit()],
            'multiple_of' => ['float', $var->getMultipleOf()]
        ]);
    }

    public function getQueryString(): string
    {
        return 'select * from ' . SetupFormula::TABLENAME_FORMULA_CONFIGURATION .' c
                left join ' . SetupFormula::TABLENAME_FORMULA_VARIABLE .' v on c.config_id = v.config_id
                where c.event_id in(%s)';
    }

    public function createEvent(array $data, array $rows): DomainEvent
    {
        $variables = [];
        foreach ($rows as $value) {
            $variables[] = new FormulaScoringVariable(
                $this->readFloat($value['min']),
                $this->readFloat($value['max']),
                $value['unit'],
                $this->readFloat($value['multiple_of'])
            );
        }

        return new QuestionPlayConfigurationSetEvent(
            $this->factory->fromString($data['question_id']),
            new ilDateTime($data['occurred_on'], IL_CAL_UNIX),
            $this->readInt($data['initiating_user_id']),
            new QuestionPlayConfiguration(
                new FormulaEditorConfiguration(),
                new FormulaScoringConfiguration(
                    $rows[0]['formula'],
                    $rows[0]['units'],
                    $this->readInt($rows[0]['precision']),
                    $this->readFloat($rows[0]['tolerance']),
                    $this->readInt($rows[0]['result_type']),
                    $variables
                )
            )
        );
    }
}