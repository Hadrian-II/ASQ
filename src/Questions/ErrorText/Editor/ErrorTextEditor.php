<?php
declare(strict_types=1);

namespace srag\asq\Questions\ErrorText\Editor;

use ILIAS\DI\UIServices;
use ilTemplate;
use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\QuestionDto;
use srag\asq\Infrastructure\Helpers\PathHelper;
use srag\asq\Questions\ErrorText\ErrorTextAnswer;
use srag\asq\Questions\ErrorText\Editor\Data\ErrorTextEditorConfiguration;
use srag\asq\UserInterface\Web\PostAccess;
use srag\asq\UserInterface\Web\Component\Editor\AbstractEditor;

/**
 * Class ErrorTextEditor
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class ErrorTextEditor extends AbstractEditor
{
    use PostAccess;
    use PathHelper;

    /**
     * @var ErrorTextEditorConfiguration
     */
    private $configuration;

    /**
     * @var UIServices
     */
    private $ui;

    public function __construct(QuestionDto $question)
    {
        global $DIC;

        $this->configuration = $question->getPlayConfiguration()->getEditorConfiguration();
        $this->ui = $DIC->ui();
        $this->ui->mainTemplate()->addCss($this->getBasePath(__DIR__) . 'css/asq.css');

        parent::__construct($question);
    }

    /**
     * @return string
     */
    public function generateHtml() : string
    {
        $tpl = new ilTemplate($this->getBasePath(__DIR__) . 'templates/default/tpl.ErrorTextEditor.html', true, true);

        $tpl->setCurrentBlock('editor');
        $tpl->setVariable('ERRORTEXT_ID', $this->getPostKey());

        if ($this->configuration->getTextSize() !== 100) {
            $tpl->setVariable('STYLE', sprintf('style="font-size: %fem"', $this->configuration->getTextSize() / 100));
        }

        $tpl->setVariable('ERRORTEXT_VALUE', is_null($this->answer) ? '' : $this->answer->getPostString());
        $tpl->setVariable('ERRORTEXT', $this->generateErrorText());
        $tpl->parseCurrentBlock();

        $this->ui->mainTemplate()->addJavaScript($this->getBasePath(__DIR__) . 'src/Questions/ErrorText/Editor/ErrorTextEditor.js');

        return $tpl->get();
    }

    /**
     * @return string
     */
    private function getPostKey() : string
    {
        return $this->question->getId()->toString();
    }

    /**
     * @return string
     */
    private function generateErrorText() : string
    {
        $matches = [];

        preg_match_all('/\S+/', $this->configuration->getSanitizedErrorText(), $matches);

        $words = $matches[0];

        $text = '';

        for ($i = 0; $i < count($words); $i++) {
            $css = 'errortext_word';
            if (!is_null($this->answer) && in_array($i, $this->answer->getSelectedWordIndexes())) {
                $css .= ' selected';
            }
            $text .= '<span class="' . $css . '" data-index="' . $i . '">' . $words[$i] . '</span> ';
        }

        return $text;
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\Domain\Definitions\IAsqQuestionEditor::readAnswer()
     */
    public function readAnswer() : AbstractValueObject
    {
        $answers = $this->getPostValue($this->getPostKey());

        if (!is_null($answers) && strlen($answers) > 0) {
            $answers = explode(',', $answers);

            $answers = array_map(function ($answer) {
                return intval($answer);
            }, $answers);

            return ErrorTextAnswer::create($answers);
        } else {
            return ErrorTextAnswer::create();
        }
    }

    /**
     * @return bool
     */
    public function isComplete() : bool
    {
        if (empty($this->configuration->getErrorText())) {
            return false;
        }

        return true;
    }
}
