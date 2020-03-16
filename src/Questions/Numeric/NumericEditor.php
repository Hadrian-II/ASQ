<?php

namespace ILIAS\AssessmentQuestion\Questions\Numeric;

use ILIAS\AssessmentQuestion\DomainModel\AbstractConfiguration;
use ILIAS\AssessmentQuestion\DomainModel\Question;
use ILIAS\AssessmentQuestion\DomainModel\Answer\Answer;
use ILIAS\AssessmentQuestion\DomainModel\QuestionDto;
use ILIAS\AssessmentQuestion\UserInterface\Web\Component\Editor\AbstractEditor;
use ILIAS\AssessmentQuestion\UserInterface\Web\Component\Editor\EmptyDisplayDefinition;
use ilNumberInputGUI;
use ilTemplate;
use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class NumericEditor
 *
 * @package ILIAS\AssessmentQuestion\Authoring\DomainModel\Question\Answer\Option;
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 * @author  Björn Heyser <bh@bjoernheyser.de>
 * @author  Martin Studer <ms@studer-raimann.ch>
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 */
class NumericEditor extends AbstractEditor {

    const VAR_MAX_NR_OF_CHARS = 'ne_max_nr_of_chars';

    /**
     * @var NumericEditorConfiguration
     */
    private $configuration;

    public function __construct(QuestionDto $question) {
        $this->configuration = $question->getPlayConfiguration()->getEditorConfiguration();
        
        parent::__construct($question);
    }

    /**
     * @return string
     */
    public function generateHtml() : string
    {
        $tpl = new ilTemplate("tpl.NumericEditor.html", true, true, "Services/AssessmentQuestion");

        $tpl->setCurrentBlock('editor');
        $tpl->setVariable('POST_NAME', $this->question->getId());
        $tpl->setVariable('NUMERIC_SIZE', $this->configuration->getMaxNumOfChars());

        if ($this->answer !== null) {
            $tpl->setVariable('CURRENT_VALUE', 'value="' . $this->answer->getValue() . '"');
        }

        $tpl->parseCurrentBlock();

        return $tpl->get();
    }

    /**
     * @return Answer
     */
    public function readAnswer() : AbstractValueObject
    {
        return NumericAnswer::create(floatval($_POST[$this->question->getId()]));
    }

    public static function generateFields(?AbstractConfiguration $config): ?array {
        /** @var NumericEditorConfiguration $config */
        global $DIC;
        
        $fields = [];

        $max_chars = new ilNumberInputGUI($DIC->language()->txt('asq_label_max_nr_of_chars'), self::VAR_MAX_NR_OF_CHARS);
        $max_chars->setInfo($DIC->language()->txt('asq_description_max_nr_chars'));
        $max_chars->setRequired(true);
        $max_chars->setSize(6);
        $fields[self::VAR_MAX_NR_OF_CHARS] = $max_chars;

        if ($config !== null) {
            $max_chars->setValue($config->getMaxNumOfChars());
        }

        return $fields;
    }

    /**
     * @return AbstractConfiguration|null
     */
    public static function readConfig() : ?AbstractConfiguration {
        return NumericEditorConfiguration::create(intval($_POST[self::VAR_MAX_NR_OF_CHARS]));
    }
    
    static function getDisplayDefinitionClass(): string {
        return EmptyDisplayDefinition::class;
    }
    
    public static function isComplete(Question $question): bool
    {
        //numeric editor always works
        return true;
    }
}