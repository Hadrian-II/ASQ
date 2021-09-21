<?php
declare(strict_types=1);

namespace srag\asq\Questions\Matching\Editor;

use ilTemplate;
use Fluxlabs\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\QuestionDto;
use srag\asq\Infrastructure\Helpers\PathHelper;
use srag\asq\Questions\Matching\MatchingAnswer;
use srag\asq\Questions\Matching\Editor\Data\MatchingEditorConfiguration;
use srag\asq\UserInterface\Web\PostAccess;
use srag\asq\UserInterface\Web\Component\Editor\AbstractEditor;

/**
 * Class MatchingEditor
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class MatchingEditor extends AbstractEditor
{
    use PostAccess;
    use PathHelper;

    public function __construct(QuestionDto $question)
    {
        parent::__construct($question);
    }

    public function additionalJSFile() : ?string
    {
        return $this->getBasePath(__DIR__) . 'src/Questions/Matching/Editor/MatchingEditor.js';
    }

    public function readAnswer() : ?AbstractValueObject
    {
        if (!$this->isPostVarSet($this->question->getId()->toString())) {
            return null;
        }

        $value = $this->getPostValue($this->question->getId()->toString());

        $matches = explode(';', $value);

        $matches = array_diff($matches, ['']);

        return new MatchingAnswer($matches);
    }

    public function generateHtml() : string
    {
        /** @var MatchingEditorConfiguration $config */
        $config = $this->question->getPlayConfiguration()->getEditorConfiguration();

        $tpl = new ilTemplate($this->getBasePath(__DIR__) . 'templates/default/tpl.MatchingEditor.html', true, true);
        $tpl->setVariable('QUESTION_ID', $this->question->getId()->toString());
        $tpl->setVariable('ANSWER', is_null($this->answer) ? '' :$this->answer->getAnswerString());
        $tpl->setVariable('MATCHING_TYPE', $config->getMatchingMode());

        $this->renderDefinitions($config, $tpl);

        $this->renderTerms($config, $tpl);

        return $tpl->get();
    }

    private function renderTerms($config, $tpl)
    {
        foreach ($config->getTerms() as $term) {
            if (!empty($term->getImage())) {
                $tpl->setCurrentBlock('term_picture');
                $tpl->setVariable('TERM', $term->getText());
                $tpl->setVariable('IMAGE', $term->getImage());
                $tpl->parseCurrentBlock();
            } else {
                $tpl->setCurrentBlock('term_text');
                $tpl->setVariable('TERM', $term->getText());
                $tpl->parseCurrentBlock();
            }

            $tpl->setCurrentBlock('draggable');
            $tpl->setVariable('ID_DRAGGABLE', $term->getId());
            $tpl->parseCurrentBlock();
        }
    }

    private function renderDefinitions($config, $tpl)
    {
        foreach ($config->getDefinitions() as $definition) {
            if (!empty($definition->getImage())) {
                $tpl->setCurrentBlock('definition_picture');
                $tpl->setVariable('DEFINITION', $definition->getText());
                $tpl->setVariable('IMAGE', $definition->getImage());
                $tpl->parseCurrentBlock();
            } else {
                $tpl->setCurrentBlock('definition_text');
                $tpl->setVariable('DEFINITION', $definition->getText());
                $tpl->parseCurrentBlock();
            }

            $tpl->setCurrentBlock('droparea');
            $tpl->setVariable('ID_DROPAREA', $definition->getId());
            $tpl->parseCurrentBlock();
        }
    }

    public function isComplete() : bool
    {
        /** @var MatchingEditorConfiguration $config */
        $config = $this->question->getPlayConfiguration()->getEditorConfiguration();

        if (count($config->getDefinitions()) < 1 ||
            count($config->getTerms()) < 1 ||
            count($config->getMatches()) < 1) {
            return false;
        }

        return true;
    }
}
