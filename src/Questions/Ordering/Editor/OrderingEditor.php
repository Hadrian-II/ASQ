<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Ordering\Editor;

use ilTemplate;
use Fluxlabs\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\Answer\Option\AnswerOption;
use srag\asq\Infrastructure\Helpers\PathHelper;
use srag\asq\Questions\Generic\Data\ImageAndTextDisplayDefinition;
use srag\asq\Questions\Ordering\OrderingAnswer;
use srag\asq\Questions\Ordering\Editor\Data\OrderingEditorConfiguration;
use srag\asq\UserInterface\Web\PostAccess;
use srag\asq\UserInterface\Web\Component\Editor\AbstractEditor;

/**
 * Class OrderingEditor
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class OrderingEditor extends AbstractEditor
{
    use PostAccess;
    use PathHelper;

    const TOUCHED_KEY = 'touched';

    private OrderingEditorConfiguration $configuration;

    private array $display_ids;

    /**
     * @param QuestionDto $question
     */
    public function __construct(QuestionDto $question, bool $is_disabled = false)
    {
        $this->configuration = $question->getPlayConfiguration()->getEditorConfiguration();

        $this->calculateDisplayIds($question);

        parent::__construct($question, $is_disabled);
    }

    public function additionalJSFile() : ?string
    {
        return $this->getBasePath(__DIR__) . 'src/Questions/Ordering/Editor/OrderingEditor.js';
    }

    private function calculateDisplayIds(QuestionDto $question)
    {
        $this->display_ids = [];

        foreach ($question->getAnswerOptions() as $option) {
            $this->display_ids[$option->getOptionId()] = md5($question->getId()->toString() . $option->getDisplayDefinition()->getText() . $option->getDisplayDefinition()->getImage());
        }
    }

    public function generateHtml() : string
    {
        $tpl = new ilTemplate($this->getBasePath(__DIR__) . 'templates/default/tpl.OrderingEditor.html', true, true);

        if (empty($this->answer)) {
            $items = $this->question->getAnswerOptions();
            shuffle($items);
        } else {
            $items = $this->orderItemsByAnswer();
        }

        foreach ($items as $item) {
            $display_definition = $item->getDisplayDefinition();

            if (!empty($display_definition->getImage())) {
                $tpl->setCurrentBlock('answer_image');
                $tpl->setVariable('ANSWER_IMAGE_URL', $display_definition->getImage());
                $tpl->setVariable('ANSWER_IMAGE_ALT', $display_definition->getText());
                $tpl->setVariable('ANSWER_IMAGE_TITLE', $display_definition->getText());
                $tpl->parseCurrentBlock();
            }

            $tpl->setCurrentBlock('item');
            $tpl->setVariable('OPTION_ID', $this->display_ids[$item->getOptionId()]);
            $tpl->setVariable('ITEM_TEXT', $display_definition->getText());
            $tpl->parseCurrentBlock();
        }

        $tpl->setCurrentBlock('editor');

        if (!$this->configuration->isVertical()) {
            $tpl->setVariable('ADD_CLASS', 'horizontal');
        }

        $tpl->setVariable('POST_NAME', $this->question->getId()->toString());
        $tpl->setVariable('POST_NAME_TOUCHED', $this->getTouchedKey());
        $tpl->setVariable('ANSWER', $this->getAnswerString($items));
        $tpl->setVariable('ENABLED', $this->is_disabled ? 'false' : 'true');
        $tpl->parseCurrentBlock();

        return $tpl->get();
    }

    private function getTouchedKey() : string
    {
        return $this->question->getId()->toString() . self::TOUCHED_KEY;
    }

    /**
     * @param AnswerOption[] $items
     * @return string
     */
    private function getAnswerString(array $items) : string
    {
        return implode(',', array_map(function ($item) {
            return $this->display_ids[$item->getOptionId()];
        }, $items));
    }

    private function orderItemsByAnswer() : array
    {
        $answers = $this->question->getAnswerOptions();

        $items = [];

        foreach ($this->answer->getSelectedOrder() as $index) {
            $items[] = $answers[$index - 1];
        }

        return $items;
    }

    public function readAnswer() : ?AbstractValueObject
    {
        if ($this->getPostValue($this->getTouchedKey()) === '' ||
            !$this->isPostVarSet($this->question->getId()->toString()))
        {
            return null;
        }

        return new OrderingAnswer(array_map(function ($display_id) {
            return array_search($display_id, $this->display_ids);
        }, explode(',', $this->getPostValue($this->question->getId()->toString()))));
    }

    public function isComplete() : bool
    {
        $options = $this->question->getAnswerOptions();

        if (count($options) < 2) {
            return false;
        }

        foreach ($options as $option) {
            /** @var ImageAndTextDisplayDefinition $option_config */
            $option_config = $option->getDisplayDefinition();

            if (empty($option_config->getText())) {
                return false;
            }
        }

        return true;
    }
}
