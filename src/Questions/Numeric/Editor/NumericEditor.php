<?php
declare(strict_types=1);

namespace srag\asq\Questions\Numeric\Editor;

use ilTemplate;
use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\QuestionDto;
use srag\asq\Infrastructure\Helpers\PathHelper;
use srag\asq\Questions\Numeric\NumericAnswer;
use srag\asq\Questions\Numeric\Editor\Data\NumericEditorConfiguration;
use srag\asq\UserInterface\Web\PostAccess;
use srag\asq\UserInterface\Web\Component\Editor\AbstractEditor;

/**
 * Class NumericEditor
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class NumericEditor extends AbstractEditor
{
    use PathHelper;
    use PostAccess;

    /**
     * @var NumericEditorConfiguration
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
        $tpl = new ilTemplate($this->getBasePath(__DIR__) . 'templates/default/tpl.NumericEditor.html', true, true);

        $tpl->setCurrentBlock('editor');
        $tpl->setVariable('POST_NAME', $this->question->getId()->toString());
        $tpl->setVariable('NUMERIC_SIZE', $this->configuration->getMaxNumOfChars());

        if ($this->answer !== null) {
            $tpl->setVariable('CURRENT_VALUE', 'value="' . $this->answer->getValue() . '"');
        }

        $tpl->parseCurrentBlock();

        return $tpl->get();
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\Domain\Definitions\IAsqQuestionEditor::readAnswer()
     */
    public function readAnswer() : AbstractValueObject
    {
        return NumericAnswer::create(floatval($this->getPostValue($this->question->getId()->toString())));
    }

    /**
     * @return bool
     */
    public function isComplete() : bool
    {
        //numeric editor always works
        return true;
    }
}
