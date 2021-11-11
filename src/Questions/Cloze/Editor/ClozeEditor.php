<?php
declare(strict_types=1);

namespace srag\asq\Questions\Cloze\Editor;

use Fluxlabs\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\QuestionDto;
use srag\asq\Questions\Cloze\ClozeAnswer;
use srag\asq\Questions\Cloze\Editor\Data\ClozeEditorConfiguration;
use srag\asq\Questions\Cloze\Editor\Data\ClozeGapConfiguration;
use srag\asq\Questions\Cloze\Editor\Data\ClozeGapItem;
use srag\asq\Questions\Cloze\Editor\Data\NumericGapConfiguration;
use srag\asq\Questions\Cloze\Editor\Data\SelectGapConfiguration;
use srag\asq\Questions\Cloze\Editor\Data\TextGapConfiguration;
use srag\asq\UserInterface\Web\Component\Editor\AbstractEditor;
use srag\asq\UserInterface\Web\PostAccess;

/**
 * Class ClozeEditor
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class ClozeEditor extends AbstractEditor
{
    use PostAccess;

    private ClozeEditorConfiguration $configuration;

    public function __construct(QuestionDto $question, bool $is_disabled = false)
    {
        $this->configuration = $question->getPlayConfiguration()->getEditorConfiguration();

        parent::__construct($question, $is_disabled);
    }

    public function readAnswer() : AbstractValueObject
    {
        $answers = [];

        for ($i = 1; $i <= count($this->configuration->getGaps()); $i += 1) {
            $answers[$i] = $this->getPostValue($this->getPostVariable($i));
        }

        $this->answer = new ClozeAnswer($answers);

        return $this->answer;
    }

    public function generateHtml() : string
    {
        $output = $this->configuration->getClozeText();

        for ($i = 1; $i <= count($this->configuration->getGaps()); $i += 1) {
            $gap_config = $this->configuration->getGaps()[$i - 1];

            if (get_class($gap_config) === SelectGapConfiguration::class) {
                $output = $this->createDropdown($i, $gap_config, $output);
            } elseif (get_class($gap_config) === TextGapConfiguration::class) {
                $output = $this->createText($i, $gap_config, $output);
            } elseif (get_class($gap_config) === NumericGapConfiguration::class) {
                $output = $this->createText($i, $gap_config, $output);
            }
        }

        return $output;
    }

    private function createDropdown(int $index, ClozeGapConfiguration $gap_config, string $output) : string
    {
        $name = '├' . $index . '┤';

        $html = sprintf(
            '<select length="20" name="%s" %s>%s</select>',
            $this->getPostVariable($index),
            $this->is_disabled ? 'disabled="disabled"' : '',
            $this->createOptions($gap_config->getItems(), $index)
        );

        return str_replace($name, $html, $output);
    }

    /**
     * @param ClozeGapItem[] $gap_items
     * @param int $index
     * @return string
     */
    private function createOptions(array $gap_items, int $index) : string
    {
        return implode(array_map(
            function (ClozeGapItem $gap_item) use ($index) {
                return sprintf(
                    '<option value="%1$s" %2$s>%1$s</option>',
                    $gap_item->getText(),
                    $gap_item->getText() === $this->getAnswer($index) ? 'selected="selected"' : ''
                );
            },
            $gap_items
        ));
    }

    private function createText(int $index, ClozeGapConfiguration $gap_config, string $output) : string
    {
        $name = '├' . $index . '┤';

        $html = sprintf(
            '<input type="text" length="20" name="%s" value="%s" style="width: %spx;" %s/>',
            $this->getPostVariable($index),
            $this->getAnswer($index) ?? '',
            $gap_config->getFieldLength(),
            $this->is_disabled ? 'disabled="disabled"' : ''
        );

        return str_replace($name, $html, $output);
    }

    /**
     * @param int $key
     * @return ?ClozeAnswer
     */
    private function getAnswer(int $key) : ?string
    {
        if (is_null($this->answer) || ! array_key_exists($key, $this->answer->getAnswers())) {
            return null;
        }

        return $this->answer->getAnswers()[$key];
    }

    private function getPostVariable(int $index) : string
    {
        return $index . $this->question->getId()->toString();
    }

    public function isComplete() : bool
    {
        if (empty($this->configuration->getClozeText())) {
            return false;
        }

        if (is_null($this->configuration->getGaps()) ||
            count($this->configuration->getGaps()) < 1) {
            return false;
        }

        foreach ($this->configuration->getGaps() as $gap_config) {
            if (!$gap_config->isComplete()) {
                return false;
            }
        }

        return true;
    }
}
