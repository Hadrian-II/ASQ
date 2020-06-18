<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Essay\Form;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\Model\Scoring\TextScoring;
use srag\asq\Questions\Essay\EssayScoringConfiguration;
use srag\asq\Questions\Essay\EssayScoringDefinition;
use srag\asq\UserInterface\Web\Form\AbstractObjectFactory;
use srag\asq\UserInterface\Web\Fields\AsqTableInputFieldDefinition;
use srag\asq\UserInterface\Web\Fields\AsqTableInput;
use ilRadioGroupInputGUI;
use ilRadioOption;
use ilNumberInputGUI;
use srag\asq\Domain\Model\Answer\Option\AnswerOptions;
use srag\asq\Questions\Essay\EssayScoring;

/**
 * Class EssayScoringConfigurationFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class EssayScoringConfigurationFactory extends AbstractObjectFactory
{
    const VAR_TEXT_MATCHING = 'es_text_matching';
    const VAR_SCORING_MODE = 'es_scoring_mode';
    const VAR_POINTS = 'es_points';
    const VAR_DEF_POINTS = 'es_def_points';
    const VAR_DEF_TEXT = 'es_def_text';

    const VAR_ANSWERS_ANY = 'es_answers_any';
    const VAR_ANSWERS_ALL = 'es_answers_all';
    const VAR_ANSWERS_ONE = 'es_answers_one';
    const VAR_ANSWERS_COUNT = 'es_answers_count';

    /**
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Form\IObjectFactory::getFormfields()
     */
    public function getFormfields(?AbstractValueObject $value): array
    {
        $fields = [];

        $text_matching = TextScoring::getScoringTypeSelectionField(self::VAR_TEXT_MATCHING);
        $fields[self::VAR_TEXT_MATCHING] = $text_matching;

        $scoring_mode = new ilRadioGroupInputGUI($this->language->txt('asq_label_text_matching'), self::VAR_SCORING_MODE);
        $scoring_mode->setRequired(true);

        $manual = new ilRadioOption($this->language->txt('asq_label_manual_scoring'), EssayScoring::SCORING_MANUAL);
        $manual->setInfo($this->language->txt('asq_info_manual_scoring'));
        $scoring_mode->addOption($manual);

        $any = new ilRadioOption($this->language->txt('asq_label_automatic_any'), EssayScoring::SCORING_AUTOMATIC_ANY);
        $any->setInfo($this->language->txt('asq_info_automatic_any'));
        $any_options = new AsqTableInput($this->language->txt('asq_label_answers'),
            self::VAR_ANSWERS_ANY,
            self::readAnswerOptionValues($value->getDefinitions()),
            [
                new AsqTableInputFieldDefinition($this->language->txt('asq_label_answer_text'),
                    AsqTableInputFieldDefinition::TYPE_TEXT,
                    self::VAR_DEF_TEXT),
                new AsqTableInputFieldDefinition($this->language->txt('asq_label_points'),
                    AsqTableInputFieldDefinition::TYPE_NUMBER,
                    self::VAR_DEF_POINTS)
            ]);
        $any->addSubItem($any_options);
        $scoring_mode->addOption($any);

        $all = new ilRadioOption($this->language->txt('asq_label_automatic_all'), EssayScoring::SCORING_AUTOMATIC_ALL);
        $all->setInfo($this->language->txt('asq_info_automatic_all'));
        $all_options = new AsqTableInput($this->language->txt('asq_label_answers'),
            self::VAR_ANSWERS_ALL,
            self::readAnswerOptionValues($value->getDefinitions()),
            [
                new AsqTableInputFieldDefinition($this->language->txt('asq_label_answer_text'),
                    AsqTableInputFieldDefinition::TYPE_TEXT,
                    self::VAR_DEF_TEXT)
            ]);

        $all_points = new ilNumberInputGUI($this->language->txt('asq_label_points'), self::VAR_ANSWERS_ALL . self::VAR_POINTS);
        $all_points->setSize(2);
        $all_points->setRequired(true);

        $all->addSubItem($all_options);
        $all->addSubItem($all_points);
        $scoring_mode->addOption($all);

        $one = new ilRadioOption($this->language->txt('asq_label_automatic_one'), EssayScoring::SCORING_AUTOMATIC_ONE);
        $one->setInfo($this->language->txt('asq_info_automatic_one'));

        $one_options = new AsqTableInput($this->language->txt('asq_label_answers'),
            self::VAR_ANSWERS_ONE,
            self::readAnswerOptionValues($value->getDefinitions()),
            [
                new AsqTableInputFieldDefinition($this->language->txt('asq_label_answer_text'),
                    AsqTableInputFieldDefinition::TYPE_TEXT,
                    self::VAR_DEF_TEXT)
            ]);

        $one_points = new ilNumberInputGUI($this->language->txt('asq_label_points'), self::VAR_ANSWERS_ONE . self::VAR_POINTS);
        $one_points->setSize(2);
        $one_points->setRequired(true);

        $one->addSubItem($one_options);
        $one->addSubItem($one_points);
        $scoring_mode->addOption($one);

        $fields[self::VAR_SCORING_MODE] = $scoring_mode;

        if ($value !== null) {
            $text_matching->setValue($value->getMatchingMode());
            $scoring_mode->setValue($value->getScoringMode());
            $all_points->setValue($value->getPoints());
            $one_points->setValue($value->getPoints());
        }

        return $fields;
    }

    /**
     * @param Answeroptions $options
     * @return array
     */
    private static function readAnswerOptionValues(?Answeroptions $options) : array {
        if (is_null($options) || count($options->getOptions()) === 0) {
            return [];
        }

        $values = [];

        foreach($options->getOptions() as $option) {
            /** @var EssayScoringDefinition $definition */
            $definition = $option->getScoringDefinition();

            $new_item = [];
            $new_item[self::VAR_DEF_TEXT] = $definition->getText();
            $new_item[self::VAR_DEF_POINTS] = $definition->getPoints();
            $values[] = $new_item;
        }

        return $values;
    }

    /**
     * @return EssayScoringConfiguration
     */
    public function readObjectFromPost(): AbstractValueObject
    {
        $scoring_mode = $this->readInt(self::VAR_SCORING_MODE);
        $points = 0.0;

        if ($scoring_mode === EssayScoring::SCORING_AUTOMATIC_ALL) {
            $points = $this->readFloat(self::VAR_ANSWERS_ALL . self::VAR_POINTS);
        }
        else if ($scoring_mode === EssayScoring::SCORING_AUTOMATIC_ONE) {
            $points = $this->readFloat(self::VAR_ANSWERS_ONE . self::VAR_POINTS);
        }

        return EssayScoringConfiguration::create(
            $this->readInt(self::VAR_TEXT_MATCHING),
            $scoring_mode,
            $points,
            $this->readDefinitions());
    }

    /**
     * @return EssayScoringDefinition[]
     */
    public function readDefinitions() : array
    {
        $selected = intval($_POST[self::VAR_SCORING_MODE]);

        $definitions = [];

        if ($selected !== EssayScoring::SCORING_MANUAL) {
            if ($selected === EssayScoring::SCORING_AUTOMATIC_ALL) {
                $prefix = self::VAR_ANSWERS_ALL;
            }
            else if ($selected === EssayScoring::SCORING_AUTOMATIC_ANY) {
                $prefix = self::VAR_ANSWERS_ANY;
            }
            else if ($selected === EssayScoring::SCORING_AUTOMATIC_ONE) {
                $prefix = self::VAR_ANSWERS_ONE;
            }

            $i = 1;

            while (array_key_exists($this->getPostKey($i, $prefix, self::VAR_DEF_TEXT), $_POST)) {
                $istr = strval($i);

                $definitions[] =
                    EssayScoringDefinition::create(
                        $this->readString($this->getPostKey($istr, $prefix, EssayScoringConfigurationFactory::VAR_DEF_TEXT)),
                        $this->readFloat($this->getPostKey($istr, $prefix, EssayScoringConfigurationFactory::VAR_DEF_POINTS))
                    );

                $i += 1;
            }
        }

        return $definitions;
    }

    /**
     * @param string $i
     * @param string $prefix
     * @param string $suffix
     * @return string
     */
    private function getPostKey($i, $prefix, $suffix) : string
    {
        return sprintf('%s_%s_%s', $i, $prefix, $suffix);
    }

    /**
     * @return EssayScoringConfiguration
     */
    public function getDefaultValue(): AbstractValueObject
    {
        return EssayScoringConfiguration::create(null, null, null, null);
    }
}