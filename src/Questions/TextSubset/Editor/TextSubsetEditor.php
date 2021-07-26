<?php
declare(strict_types = 1);

namespace srag\asq\Questions\TextSubset\Editor;

use ilTemplate;
use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\QuestionDto;
use srag\asq\Infrastructure\Helpers\PathHelper;
use srag\asq\Questions\TextSubset\TextSubsetAnswer;
use srag\asq\Questions\TextSubset\Editor\Data\TextSubsetEditorConfiguration;
use srag\asq\UserInterface\Web\PostAccess;
use srag\asq\UserInterface\Web\Component\Editor\AbstractEditor;

/**
 * Class TextSubsetEditor
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class TextSubsetEditor extends AbstractEditor
{
    use PostAccess;
    use PathHelper;

    private TextSubsetEditorConfiguration $configuration;

    public function __construct(QuestionDto $question)
    {
        $this->configuration = $question->getPlayConfiguration()->getEditorConfiguration();

        parent::__construct($question);
    }

    public function generateHtml() : string
    {
        $tpl = new ilTemplate($this->getBasePath(__DIR__) . 'templates/default/tpl.TextSubsetEditor.html', true, true);

        for ($i = 1; $i <= $this->configuration->getNumberOfRequestedAnswers(); $i++) {
            $tpl->setCurrentBlock('textsubset_row');
            $tpl->setVariable('COUNTER', $i);
            $tpl->setVariable('TEXTFIELD_ID', $this->getPostName($i));
            $tpl->setVariable('TEXTFIELD_SIZE', $this->calculateSize());

            if (!is_null($this->answer) && array_key_exists($i, $this->answer->getAnswers()) && !is_null($this->answer->getAnswers()[$i])) {
                $tpl->setVariable('TEXTFIELD_VALUE', 'value="' . $this->answer->getAnswers()[$i] . '"');
            }

            $tpl->parseCurrentBlock();
        }

        return $tpl->get();
    }

    private function getPostName(int $i) : string
    {
        return $i . $this->question->getId()->toString();
    }

    private function calculateSize() : int
    {
        $max = 1;
        foreach ($this->question->getAnswerOptions() as $option) {
            max($max, strlen($option->getScoringDefinition()->getText()));
        }

        return $max += 10 - ($max % 10);
    }

    public function readAnswer() : ?AbstractValueObject
    {
        if (!$this->isPostVarSet($this->getPostName(1))) {
            return null;
        }

        $answer = [];

        for ($i = 1; $i <= $this->configuration->getNumberOfRequestedAnswers(); $i++) {
            $answer[$i] = $this->getPostValue($this->getPostName($i));
        }

        return new TextSubsetAnswer($answer);
    }

    public function isComplete() : bool
    {
        if (empty($this->configuration->getNumberOfRequestedAnswers())) {
            return false;
        }

        return true;
    }
}
