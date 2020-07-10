<?php
declare(strict_types = 1);

namespace srag\asq\Questions\TextSubset\Editor;

use ilTemplate;
use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\PathHelper;
use srag\asq\Domain\QuestionDto;
use srag\asq\Questions\Generic\Data\EmptyDefinition;
use srag\asq\Questions\TextSubset\TextSubsetAnswer;
use srag\asq\Questions\TextSubset\Editor\Data\TextSubsetEditorConfiguration;
use srag\asq\UserInterface\Web\Component\Editor\AbstractEditor;
use srag\asq\UserInterface\Web\Form\InputHandlingTrait;

/**
 * Class TextSubsetEditor
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 * @package srag/asq
 * @author Adrian Lüthi <al@studer-raimann.ch>
 */
class TextSubsetEditor extends AbstractEditor
{
    use InputHandlingTrait;
    use PathHelper;

    /**
     * @var TextSubsetEditorConfiguration
     */
    private $configuration;

    /**
     * @param QuestionDto $question
     */
    public function __construct(QuestionDto $question)
    {
        $this->configuration = $question->getPlayConfiguration()->getEditorConfiguration();

        parent::__construct($question);
    }

    /**
     * @return string
     */
    public function generateHtml() : string
    {
        $tpl = new ilTemplate($this->getBasePath(__DIR__) . 'templates/default/tpl.TextSubsetEditor.html', true, true);

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
    private function getPostValue(int $i) : string
    {
        return $i . $this->question->getId();
    }

    /**
     * @return int
     */
    private function calculateSize() : int
    {
        $max = 1;
        foreach ($this->question->getAnswerOptions()->getOptions() as $option) {
            max($max, strlen($option->getScoringDefinition()->getText()));
        }

        return $max += 10 - ($max % 10);
    }

    /**
     * @return ?AbstractValueObject
     */
    public function readAnswer() : ?AbstractValueObject
    {
        $value = $this->readString($this->getPostValue(1));

        if (empty($value)) {
            return null;
        }

        $answer = [];

        for ($i = 1; $i <= $this->configuration->getNumberOfRequestedAnswers(); $i++) {
            $answer[$i] = $this->readString($this->getPostValue($i));
        }

        return TextSubsetAnswer::create($answer);
    }

    /**
     * @return string
     */
    public static function getDisplayDefinitionClass() : string
    {
        return EmptyDefinition::class;
    }

    /**
     * @return bool
     */
    public function isComplete() : bool
    {
        if (empty($this->configuration->getNumberOfRequestedAnswers())) {
            return false;
        }

        return true;
    }
}
