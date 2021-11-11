<?php
declare(strict_types=1);

namespace srag\asq\Questions\Numeric\Editor;

use ilTemplate;
use Fluxlabs\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\QuestionDto;
use srag\asq\Infrastructure\Helpers\PathHelper;
use srag\asq\Questions\Numeric\NumericAnswer;
use srag\asq\Questions\Numeric\Editor\Data\NumericEditorConfiguration;
use srag\asq\UserInterface\Web\PostAccess;
use srag\asq\UserInterface\Web\Component\Editor\AbstractEditor;
use srag\asq\UserInterface\Web\Form\InputHandlingTrait;

/**
 * Class NumericEditor
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class NumericEditor extends AbstractEditor
{
    use PathHelper;
    use PostAccess;
    use InputHandlingTrait;

    private NumericEditorConfiguration $configuration;

    public function __construct(QuestionDto $question, bool $is_disabled = false)
    {
        $this->configuration = $question->getPlayConfiguration()->getEditorConfiguration();

        parent::__construct($question, $is_disabled);
    }

    public function generateHtml() : string
    {
        $tpl = new ilTemplate($this->getBasePath(__DIR__) . 'templates/default/tpl.NumericEditor.html', true, true);

        $tpl->setCurrentBlock('editor');
        $tpl->setVariable('POST_NAME', $this->question->getId()->toString());
        $tpl->setVariable('NUMERIC_SIZE', $this->configuration->getMaxNumOfChars());
        $tpl->setVariable('DISABLED', $this->is_disabled ? 'disabled="disabled"' : '');

        if ($this->answer !== null) {
            $tpl->setVariable('CURRENT_VALUE', 'value="' . $this->answer->getValue() . '"');
        }

        $tpl->parseCurrentBlock();

        return $tpl->get();
    }

    public function readAnswer() : AbstractValueObject
    {
        return new NumericAnswer($this->readFloat($this->getPostValue($this->question->getId()->toString())));
    }

    public function isComplete() : bool
    {
        //numeric editor always works
        return true;
    }
}
