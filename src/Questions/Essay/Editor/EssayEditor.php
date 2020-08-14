<?php
declare(strict_types=1);

namespace srag\asq\Questions\Essay\Editor;

use ILIAS\DI\UIServices;
use ilTemplate;
use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\PathHelper;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\Answer\Answer;
use srag\asq\Questions\Essay\EssayAnswer;
use srag\asq\Questions\Essay\Editor\Data\EssayEditorConfiguration;
use srag\asq\Questions\Generic\Data\EmptyDefinition;
use srag\asq\UserInterface\Web\Component\Editor\AbstractEditor;
use srag\asq\UserInterface\Web\PostAccess;

/**
 * Class EssayEditor
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class EssayEditor extends AbstractEditor
{
    use PostAccess;
    use PathHelper;

    /**
     * @var EssayEditorConfiguration
     */
    private $configuration;

    /**
     * @var UIServices
     */
    private $ui;

    /**
     * @var ilLanguage;
     */
    private $language;

    /**
     * @param QuestionDto $question
     */
    public function __construct(QuestionDto $question)
    {
        global $DIC;

        $this->configuration = $question->getPlayConfiguration()->getEditorConfiguration();
        $this->ui = $DIC->ui();
        $this->language = $DIC->language();

        parent::__construct($question);
    }

    /**
     * @return string
     */
    public function generateHtml() : string
    {
        $tpl = new ilTemplate($this->getBasePath(__DIR__) . 'templates/default/tpl.EssayEditor.html', true, true);

        $tpl->setVariable('ESSAY', is_null($this->answer) ? '' : $this->answer->getText());
        $tpl->setVariable('POST_VAR', $this->question->getId());

        if (!empty($this->configuration->getMaxLength())) {
            $tpl->setCurrentBlock('maximum_char_hint');
            $tpl->setVariable('MAXIMUM_CHAR_HINT', $this->language->txt('asq_max_characters'));
            $tpl->setVariable('MAX_LENGTH', $this->configuration->getMaxLength());
            $tpl->setVariable('ERROR_MESSAGE', $this->language->txt('asq_error_too_long'));
            $tpl->parseCurrentBlock();

            $tpl->setCurrentBlock('maxchars_counter');
            $tpl->setVariable('CHARACTERS', $this->language->txt('asq_char_count'));
            $tpl->parseCurrentBlock();
        }

        // TODO wordcount??
        if (false) {
            $tpl->setCurrentBlock('maxchars_counter');
            $tpl->setVariable('CHARACTERS', $this->language->txt('asq_char_count'));
            $tpl->parseCurrentBlock();
        }

        $this->ui->mainTemplate()->addJavaScript($this->getBasePath(__DIR__) . 'src/Questions/Essay/EssayEditor.js');

        return $tpl->get();
    }

    /**
     * @return Answer
     */
    public function readAnswer() : AbstractValueObject
    {
        return EssayAnswer::create($this->getPostValue($this->question->getId()));
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
        // no necessary values
        return true;
    }
}
