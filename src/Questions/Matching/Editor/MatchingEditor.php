<?php
declare(strict_types=1);

namespace srag\asq\Questions\Matching\Editor;

use ILIAS\DI\UIServices;
use ilTemplate;
use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\PathHelper;
use srag\asq\Domain\QuestionDto;
use srag\asq\Questions\Generic\Data\EmptyDefinition;
use srag\asq\Questions\Matching\MatchingAnswer;
use srag\asq\Questions\Matching\Editor\Data\MatchingEditorConfiguration;
use srag\asq\UserInterface\Web\PostAccess;
use srag\asq\UserInterface\Web\Component\Editor\AbstractEditor;

/**
 * Class MatchingEditor
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class MatchingEditor extends AbstractEditor
{
    use PostAccess;
    use PathHelper;

    /**
     * @var UIServices
     */
    private $ui;

    /**
     * @param QuestionDto $question
     */
    public function __construct(QuestionDto $question)
    {
        global $DIC;

        $this->ui = $DIC->ui();

        parent::__construct($question);
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Component\Editor\AbstractEditor::readAnswer()
     */
    public function readAnswer() : ?AbstractValueObject
    {


        if (!$this->isPostVarSet($this->question->getId())) {
            return null;
        }

        $value = $this->getPostValue($this->question->getId());

        $matches = explode(';', $value);

        $matches = array_diff($matches, ['']);

        return MatchingAnswer::create($matches);
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Component\Editor\AbstractEditor::generateHtml()
     */
    public function generateHtml() : string
    {
        /** @var MatchingEditorConfiguration $config */
        $config = $this->question->getPlayConfiguration()->getEditorConfiguration();

        $tpl = new ilTemplate($this->getBasePath(__DIR__) . 'templates/default/tpl.MatchingEditor.html', true, true);
        $tpl->setVariable('QUESTION_ID', $this->question->getId());
        $tpl->setVariable('ANSWER', is_null($this->answer) ? '' :$this->answer->getAnswerString());
        $tpl->setVariable('MATCHING_TYPE', $config->getMatchingMode());

        $this->renderDefinitions($config, $tpl);

        $this->renderTerms($config, $tpl);

        $this->ui->mainTemplate()->addJavaScript($this->getBasePath(__DIR__) . 'src/Questions/Matching/Editor/MatchingEditor.js');

        return $tpl->get();
    }

    /**
     * @param config
     * @param tpl
     */
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

    /**
     * @param config
     * @param tpl
     */
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
