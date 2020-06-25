<?php
declare(strict_types=1);

namespace srag\asq\Questions\Cloze;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\Answer\Option\EmptyDefinition;
use srag\asq\UserInterface\Web\Component\Editor\AbstractEditor;
use srag\asq\UserInterface\Web\Form\InputHandlingTrait;

/**
 * Class ClozeEditor
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class ClozeEditor extends AbstractEditor
{
    use InputHandlingTrait;

    /**
     * @var ClozeEditorConfiguration
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
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Component\Editor\AbstractEditor::readAnswer()
     */
    public function readAnswer(): AbstractValueObject
    {
        $answers = [];

        for ($i = 1; $i <= count($this->configuration->getGaps()); $i += 1) {
            $answers[$i] = $this->readString($this->getPostVariable($i));
        }

        $this->answer = ClozeAnswer::create($answers);

        return $this->answer;
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Component\Editor\AbstractEditor::generateHtml()
     */
    public function generateHtml(): string
    {
        $output = $this->configuration->getClozeText();

        for ($i = 1; $i <= count($this->configuration->getGaps()); $i += 1) {
            $gap_config = $this->configuration->getGaps()[$i - 1];

            if (get_class($gap_config) === SelectGapConfiguration::class) {
                $output = $this->createDropdown($i, $gap_config, $output);
            }
            else if (get_class($gap_config) === TextGapConfiguration::class) {
                $output = $this->createText($i, $gap_config, $output);
            }
            else if (get_class($gap_config) === NumericGapConfiguration::class) {
                $output = $this->createText($i, $gap_config, $output);
            }
        }

        return $output;
    }

    /**
     * @param int $index
     * @param ClozeGapConfiguration $gap_config
     * @param string $output
     * @return string
     */
    private function createDropdown(int $index, ClozeGapConfiguration $gap_config, string $output) : string
    {
        $name = '{' . $index . '}';

        $html = sprintf('<select length="20" name="%s">%s</select>',
            $this->getPostVariable($index),
            $this->createOptions($gap_config->getItems(), $index));

        return str_replace($name, $html, $output);
    }

    /**
     * @param ClozeGapItem[] $gapItems
     * @return string
     */
    private function createOptions(array $gap_items, int $index) : string
    {
        return implode(array_map(
            function(ClozeGapItem $gap_item) use ($index) {
                return sprintf('<option value="%1$s" %2$s>%1$s</option>',
                               $gap_item->getText(),
                               $gap_item->getText() === $this->getAnswer($index) ? 'selected="selected"' : '');
            },
            $gap_items
        ));
    }

    /**
     * @param int $index
     * @param ClozeGapConfiguration $gap_config
     * @param string $output
     * @return string
     */
    private function createText(int $index, ClozeGapConfiguration $gap_config, string $output) : string
    {
        $name = '{' . $index . '}';

        $html = sprintf('<input type="text" length="20" name="%s" value="%s" style="width: %spx;" />',
            $this->getPostVariable($index),
            $this->getAnswer($index) ?? '',
            $gap_config->getFieldLength());

        return str_replace($name, $html, $output);
    }

    /**
     * @param int $key
     * @return NULL|ClozeAnswer
     */
    private function getAnswer(int $key)
    {
        if (is_null($this->answer)) {
            return null;
        }

        return $this->answer->getAnswers()[$key];
    }

    /**
     * @param int $index
     * @return string
     */
    private function getPostVariable(int $index) : string
    {
        return $index . $this->question->getId();
    }

    /**
     * @return bool
     */
    public function isComplete(): bool
    {
        if (empty($this->configuration->getClozeText())) {
            return false;
        }

        if (is_null($this->configuration->getGaps() ||
            count($this->configuration->getGaps() < 1))) {
            return false;
        }

        foreach ($this->configuration->getGaps() as $gap_config) {
            if (! $gap_config->isComplete()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return string
     */
    static function getDisplayDefinitionClass() : string {
        return EmptyDefinition::class;
    }
}