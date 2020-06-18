<?php
declare(strict_types=1);

namespace srag\asq\Questions\Essay;

use ilTemplate;
use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\Answer\Answer;
use srag\asq\Domain\Model\Answer\Option\EmptyDefinition;
use srag\asq\UserInterface\Web\PathHelper;
use srag\asq\UserInterface\Web\Component\Editor\AbstractEditor;
use srag\asq\UserInterface\Web\Form\InputHandlingTrait;

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
    use InputHandlingTrait;
    use PathHelper;

    /**
     * @var EssayEditorConfiguration
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
        global $DIC;

        $tpl = new ilTemplate($this->getBasePath(__DIR__) . 'templates/default/tpl.EssayEditor.html', true, true);

        $tpl->setVariable('ESSAY', is_null($this->answer) ? '' : $this->answer->getText());
        $tpl->setVariable('POST_VAR', $this->question->getId());

        if (!empty($this->configuration->getMaxLength())) {
            $tpl->setCurrentBlock('maximum_char_hint');
            $tpl->setVariable('MAXIMUM_CHAR_HINT', $DIC->language()->txt('asq_max_characters'));
            $tpl->setVariable('MAX_LENGTH', $this->configuration->getMaxLength());
            $tpl->setVariable('ERROR_MESSAGE', $DIC->language()->txt('asq_error_too_long'));
            $tpl->parseCurrentBlock();

            $tpl->setCurrentBlock('maxchars_counter');
            $tpl->setVariable('CHARACTERS', $DIC->language()->txt('asq_char_count'));
            $tpl->parseCurrentBlock();
        }

        // TODO wordcount??
        if (false) {
            $tpl->setCurrentBlock('maxchars_counter');
            $tpl->setVariable('CHARACTERS', $DIC->language()->txt('asq_'));
            $tpl->parseCurrentBlock();
        }

        $DIC->ui()->mainTemplate()->addJavaScript($this->getBasePath(__DIR__) . 'src/Questions/Essay/EssayEditor.js');

        return $tpl->get();
    }

    /**
     * @return Answer
     */
    public function readAnswer() : AbstractValueObject
    {
        return EssayAnswer::create($this->readString($this->question->getId()));
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
        // no necessary values
        return true;
    }
}