<?php
declare(strict_types=1);

namespace srag\asq\Questions\ErrorText\Editor;

use ilTemplate;
use Fluxlabs\CQRS\Aggregate\AbstractValueObject;
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
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class ErrorTextEditor extends AbstractEditor
{
    use PostAccess;
    use PathHelper;

    private ErrorTextEditorConfiguration $configuration;

    public function __construct(QuestionDto $question, bool $is_disabled = false)
    {
        $this->configuration = $question->getPlayConfiguration()->getEditorConfiguration();

        parent::__construct($question, $is_disabled);
    }

    public function additionalJSFile() : ?string
    {
        return $this->getBasePath(__DIR__) . 'src/Questions/ErrorText/Editor/ErrorTextEditor.js';
    }

    public function generateHtml() : string
    {
        $tpl = new ilTemplate($this->getBasePath(__DIR__) . 'templates/default/tpl.ErrorTextEditor.html', true, true);

        $tpl->setCurrentBlock('editor');
        $tpl->setVariable('ERRORTEXT_ID', $this->getPostKey());

        if ($this->configuration->getTextSize() !== 100) {
            $tpl->setVariable('STYLE', sprintf('style="font-size: %fem"', $this->configuration->getTextSize() / 100));
        }

        $tpl->setVariable('ENABLED', $this->is_disabled ? 'false' : 'true');

        $tpl->setVariable('ERRORTEXT_VALUE', is_null($this->answer) ? '' : $this->answer->getPostString());
        $tpl->setVariable('ERRORTEXT', $this->generateErrorText());
        $tpl->parseCurrentBlock();

        return $tpl->get();
    }

    private function getPostKey() : string
    {
        return $this->question->getId()->toString();
    }

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

            $word = preg_replace('/[^a-z0-9]+/i', '', $words[$i]);
            $punctuation = substr($words[$i], strlen($word));

            $text .= '<span class="' . $css . '" data-index="' . $i . '">' . $word . '</span>' . $punctuation . ' ';
        }

        return $text;
    }

    public function readAnswer() : ?AbstractValueObject
    {
        $answers = $this->getPostValue($this->getPostKey());

        if (!is_null($answers) && strlen($answers) > 0) {
            $answers = explode(',', $answers);

            $answers = array_map(function ($answer) {
                return intval($answer);
            }, $answers);

            return new ErrorTextAnswer($answers);
        } else {
            return null;
        }
    }

    public function isComplete() : bool
    {
        if (empty($this->configuration->getErrorText())) {
            return false;
        }

        return true;
    }
}
