<?php
declare(strict_types = 1);
namespace srag\asq\Questions\Ordering;

use ilTemplate;
use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\Answer\Option\AnswerOption;
use srag\asq\Domain\Model\Answer\Option\ImageAndTextDisplayDefinition;
use srag\asq\UserInterface\Web\PathHelper;
use srag\asq\UserInterface\Web\Component\Editor\AbstractEditor;
use srag\asq\UserInterface\Web\Form\InputHandlingTrait;

/**
 * Class OrderingEditor
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 * @package srag/asq
 * @author Adrian Lüthi <al@studer-raimann.ch>
 */
class OrderingEditor extends AbstractEditor
{
    use InputHandlingTrait;
    use PathHelper;

    /**
     * @var OrderingEditorConfiguration
     */
    private $configuration;

    /**
     * @var array
     */
    private $display_ids;

    /**
     * @param QuestionDto $question
     */
    public function __construct(QuestionDto $question)
    {
        $this->configuration = $question->getPlayConfiguration()->getEditorConfiguration();

        $this->calculateDisplayIds($question);

        parent::__construct($question);
    }

    /**
     * @param QuestionDto $question
     */
    private function calculateDisplayIds(QuestionDto $question)
    {
        $this->display_ids = [];

        foreach ($question->getAnswerOptions()->getOptions() as $option) {
            $this->display_ids[$option->getOptionId()] = md5($question->getId() . $option->getDisplayDefinition()->getText());
        }
    }

    /**
     * @return string
     */
    public function generateHtml(): string
    {
        global $DIC;

        $tpl = new ilTemplate($this->getBasePath(__DIR__) . 'templates/default/tpl.OrderingEditor.html', true, true);

        if (empty($this->answer)) {
            $items = $this->question->getAnswerOptions()->getOptions();
            shuffle($items);
        } else {
            $items = $this->orderItemsByAnswer();
        }

        foreach ($items as $item) {
            $tpl->setCurrentBlock('item');
            $tpl->setVariable('OPTION_ID', $this->display_ids[$item->getOptionId()]);
            $tpl->setVariable('ITEM_TEXT', $item->getDisplayDefinition()
                ->getText());
            $tpl->parseCurrentBlock();
        }

        $tpl->setCurrentBlock('editor');

        if (! $this->configuration->isVertical()) {
            $tpl->setVariable('ADD_CLASS', 'horizontal');
        }

        $tpl->setVariable('POST_NAME', $this->question->getId());
        $tpl->setVariable('ANSWER', $this->getAnswerString($items));
        $tpl->parseCurrentBlock();

        $DIC->ui()
            ->mainTemplate()
            ->addJavaScript($this->getBasePath(__DIR__) . 'src/Questions/Ordering/OrderingEditor.js');

        return $tpl->get();
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

    /**
     * @return array
     */
    private function orderItemsByAnswer() : array
    {
        $answers = $this->question->getAnswerOptions()->getOptions();

        $items = [];

        foreach ($this->answer->getSelectedOrder() as $index) {
            $items[] = $answers[$index - 1];
        }

        return $items;
    }

    /**
     * {@inheritdoc}
     * @see \srag\asq\UserInterface\Web\Component\Editor\AbstractEditor::readAnswer()
     */
    public function readAnswer() : ?AbstractValueObject
    {
        $value = $this->readString($this->question->getId());

        if (empty($value)) {
            return null;
        }

        return OrderingAnswer::create(array_map(function ($display_id) {
            return array_search($display_id, $this->display_ids);
        }, explode(',', $value)));
    }

    /**
     * @return string
     */
    static function getDisplayDefinitionClass() : string
    {
        return ImageAndTextDisplayDefinition::class;
    }

    /**
     * @return bool
     */
    public function isComplete() : bool
    {
        $options = $this->question->getAnswerOptions()->getOptions();

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