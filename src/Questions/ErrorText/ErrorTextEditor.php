<?php
declare(strict_types=1);

namespace srag\asq\Questions\ErrorText;

use ilTemplate;
use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\Answer\Answer;
use srag\asq\Domain\Model\Answer\Option\EmptyDefinition;
use srag\asq\UserInterface\Web\PathHelper;
use srag\asq\UserInterface\Web\Component\Editor\AbstractEditor;
use srag\asq\UserInterface\Web\Form\InputHandlingTrait;

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
    use InputHandlingTrait;
    use PathHelper;

    /**
     * @var ErrorTextEditorConfiguration
     */
    private $configuration;

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
        global $DIC;

        $tpl = new ilTemplate($this->getBasePath(__DIR__) . 'templates/default/tpl.ErrorTextEditor.html', true, true);

        $tpl->setCurrentBlock('editor');
        $tpl->setVariable('ERRORTEXT_ID', $this->getPostKey());

        if ($this->configuration->getTextSize() !== 100) {
            $tpl->setVariable('STYLE', sprintf('style="font-size: %fem"', $this->configuration->getTextSize() / 100));
        }

        $tpl->setVariable('ERRORTEXT_VALUE', is_null($this->answer) ? '' : $this->answer->getPostString());
        $tpl->setVariable('ERRORTEXT', $this->generateErrorText());
        $tpl->parseCurrentBlock();

        $DIC->ui()->mainTemplate()->addJavaScript($this->getBasePath(__DIR__) . 'src/Questions/ErrorText/ErrorTextEditor.js');

        return $tpl->get();
    }

    /**
     * @return string
     */
    private function getPostKey() : string
    {
        return $this->question->getId();
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
     * @return Answer
     */
    public function readAnswer() : AbstractValueObject
    {
        $answers = $this->readString($this->getPostKey());

        if(!is_null($answers) && strlen($answers) > 0) {
            $answers = explode(',', $answers);

            $answers = array_map(function($answer) {
                return intval($answer);
            }, $answers);

            return ErrorTextAnswer::create($answers);
        }
        else {
            return ErrorTextAnswer::create();
        }
    }

    /**
     * @return string
     */
    static function getDisplayDefinitionClass() : string
    {
        return EmptyDefinition::class;
    }

    /**
     * @return bool
     */
    public function isComplete() : bool
    {
        if (empty($this->configuration->getErrorText()))
        {
            return false;
        }

        return true;
    }
}