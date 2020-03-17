<?php

namespace ILIAS\AssessmentQuestion\Questions\TextSubset;

use ILIAS\AssessmentQuestion\DomainModel\AbstractConfiguration;
use ILIAS\AssessmentQuestion\DomainModel\Question;
use ILIAS\AssessmentQuestion\DomainModel\QuestionDto;
use ILIAS\AssessmentQuestion\UserInterface\Web\PathHelper;
use ILIAS\AssessmentQuestion\UserInterface\Web\Component\Editor\AbstractEditor;
use ILIAS\AssessmentQuestion\UserInterface\Web\Component\Editor\EmptyDisplayDefinition;
use ilNumberInputGUI;
use ilTemplate;
use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class TextSubsetEditor
 *
 * @package ILIAS\AssessmentQuestion\Authoring\DomainModel\Question\Answer\Option;
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 * @author  Björn Heyser <bh@bjoernheyser.de>
 * @author  Martin Studer <ms@studer-raimann.ch>
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 */
class TextSubsetEditor extends AbstractEditor {
    
    const VAR_REQUESTED_ANSWERS = 'tse_requested_answers';
    
    /**
     * @var TextSubsetEditorConfiguration
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
        $tpl = new ilTemplate(PathHelper::getBasePath(__DIR__) . 'templates/default/tpl.TextSubsetEditor.html', true, true);
        
        for ($i = 1; $i <= $this->configuration->getNumberOfRequestedAnswers(); $i++) {
            $tpl->setCurrentBlock('textsubset_row');
            $tpl->setVariable('COUNTER', $i);
            $tpl->setVariable('TEXTFIELD_ID', $this->getPostValue($i));
            $tpl->setVariable('TEXTFIELD_SIZE', $this->calculateSize());
            
            if (!is_null($this->answer) && !is_null($this->answer->getAnswers()[$i])) {
                $tpl->setVariable('TEXTFIELD_VALUE', 'value="' . $this->answer->getAnswers()[$i] . '"');
            }
            
            $tpl->parseCurrentBlock();
        }

        return $tpl->get();
    }
    
    /**
     * @param int $i
     * @return string
     */
    private function getPostValue(int $i) {
        return $i . $this->question->getId();
    }
    
    /**
     * @return number
     */
    private function calculateSize() {
        $max = 1;
        foreach ($this->question->getAnswerOptions()->getOptions() as $option) {
            max($max, strlen($option->getScoringDefinition()->getText()));
        }
        
        return $max += 10 - ($max % 10);
    }
    
    /**
     * @return AbstractValueObject
     */
    public function readAnswer() : AbstractValueObject
    {
        $answer = [];
        
        for ($i = 1; $i <= $this->configuration->getNumberOfRequestedAnswers(); $i++) { 
            $answer[$i] = $_POST[$this->getPostValue($i)];
        }
        
        return TextSubsetAnswer::create($answer);
    }

    public static function generateFields(?AbstractConfiguration $config): ?array {
        /** @var TextSubsetEditorConfiguration $config */
        global $DIC;
        
        $fields = [];
        
        $requested_answers = new ilNumberInputGUI($DIC->language()->txt('asq_label_requested_answers'), self::VAR_REQUESTED_ANSWERS);
        $requested_answers->setRequired(true);
        $requested_answers->setSize(2);
        $fields[self::VAR_REQUESTED_ANSWERS] = $requested_answers;
        
        if ($config !== null) {
            $requested_answers->setValue($config->getNumberOfRequestedAnswers());
        }
        
        return $fields;
    }
    
    /**
     * @return AbstractConfiguration|null
     */
    public static function readConfig() : ?AbstractConfiguration {
        return TextSubsetEditorConfiguration::create(intval($_POST[self::VAR_REQUESTED_ANSWERS]));
    }
    
    /**
     * @return string
     */
    static function getDisplayDefinitionClass() : string {
        return EmptyDisplayDefinition::class;
    }
    
    /**
     * @param Question $question
     * @return bool
     */
    public static function isComplete(Question $question): bool
    {
        /** @var TextSubsetEditorConfiguration $config */
        $config = $question->getPlayConfiguration()->getEditorConfiguration();
        
        if (empty($config->getNumberOfRequestedAnswers())) {
            return false;
        }
        
        return true;
    }
}