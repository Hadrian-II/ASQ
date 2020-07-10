<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Choice\Editor\ImageMap;

use ILIAS\DI\UIServices;
use Exception;
use ilTemplate;
use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\PathHelper;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\Answer\Answer;
use srag\asq\Domain\Model\Answer\Option\AnswerOption;
use srag\asq\Questions\Choice\MultipleChoiceAnswer;
use srag\asq\Questions\Choice\Editor\ImageMap\Data\ImageMapEditorConfiguration;
use srag\asq\Questions\Choice\Editor\ImageMap\Data\ImageMapEditorDefinition;
use srag\asq\UserInterface\Web\Component\Editor\AbstractEditor;
use srag\asq\UserInterface\Web\Form\InputHandlingTrait;

/**
 * Class ImageMapEditor
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 * @package srag/asq
 * @author Adrian Lüthi <al@studer-raimann.ch>
 */
class ImageMapEditor extends AbstractEditor
{
    use InputHandlingTrait;
    use PathHelper;

    /**
     * @var ImageMapEditorConfiguration
     */
    private $configuration;

    /**
     * @var UIServices
     */
    private $ui;

    /**
     * @param QuestionDto $question
     */
    public function __construct(QuestionDto $question)
    {
        global $DIC;

        $this->configuration = $question->getPlayConfiguration()->getEditorConfiguration();
        $this->ui = $DIC->ui();

        parent::__construct($question);
    }

    /**
     * @return string
     */
    public function generateHtml() : string
    {
        $tpl = new ilTemplate($this->getBasePath(__DIR__) . 'templates/default/tpl.ImageMapEditor.html', true, true);

        $tpl->setCurrentBlock('generic');
        $tpl->setVariable('POST_NAME', $this->getPostName());
        $tpl->setVariable('IMAGE_URL', $this->configuration->getImage());
        $tpl->setVariable('VALUE', is_null($this->answer) ? '' : implode(',', $this->answer->getSelectedIds()));
        $tpl->setVariable('MAX_ANSWERS', $this->configuration->getMaxAnswers());
        $tpl->parseCurrentBlock();

        /** @var AnswerOption $answer_option */
        foreach ($this->question->getAnswerOptions()->getOptions() as $answer_option) {
            /** @var ImageMapEditorDefinition $display_definition */
            $display_definition = $answer_option->getDisplayDefinition();

            $tpl->setCurrentBlock('answer_option');
            $tpl->setVariable('OPTION_SHAPE', $this->generateShape($display_definition, $answer_option->getOptionId()));
            $tpl->parseCurrentBlock();
        }

        $this->ui
            ->mainTemplate()
            ->addJavaScript($this->getBasePath(__DIR__) . 'src/Questions/ImageMap/ImageMapEditor.js');

        return $tpl->get();
    }

    /**
     * @return string
     */
    private function getPostName() : string
    {
        return $this->question->getId();
    }

    /**
     * @param ImageMapEditorDefinition $display_definition
     * @param int $id
     * @return string
     */
    private function generateShape(ImageMapEditorDefinition $display_definition, string $id) : string
    {
        switch ($display_definition->getType()) {
            case ImageMapEditorDefinition::TYPE_CIRCLE:
                return $this->generateCircle($display_definition, $id);
            case ImageMapEditorDefinition::TYPE_POLYGON:
                return $this->generatePolygon($display_definition, $id);
            case ImageMapEditorDefinition::TYPE_RECTANGLE:
                return $this->generateRectangle($display_definition, $id);
            default:
                throw new Exception('implement rendering of shape please');
        }
    }

    /**
     * @param ImageMapEditorDefinition $display_definition
     * @param int $id
     * @return string
     */
    private function generateCircle(ImageMapEditorDefinition $display_definition, string $id) : string
    {
        $values = $this->decodeCoordinates($display_definition->getCoordinates());

        return '<ellipse class="' . $this->getClass($id) . '"
                      cx="' . $values['cx'] . '"
                      cy="' . $values['cy'] . '"
                      rx="' . $values['rx'] . '"
                      ry="' . $values['ry'] . '"
                      data-value="' . $id . '">
                   <title>' . $display_definition->getTooltip() . '</title>
                </ellipse>';
    }

    /**
     * @param ImageMapEditorDefinition $display_definition
     * @param int $id
     * @return string
     */
    private function generatePolygon(ImageMapEditorDefinition $display_definition, string $id) : string
    {
        $values = $this->decodeCoordinates($display_definition->getCoordinates());

        return '<polygon class="' . $this->getClass($id) . '" points="' . $values['points'] . '" data-value="' . $id . '">
                   <title>' . $display_definition->getTooltip() . '</title>
                </polygon>';
    }

    /**
     * @param ImageMapEditorDefinition $display_definition
     * @param int $id
     * @return string
     */
    private function generateRectangle(ImageMapEditorDefinition $display_definition, string $id) : string
    {
        $values = $this->decodeCoordinates($display_definition->getCoordinates());

        return '<rect class="' . $this->getClass($id) . '"
                      x="' . $values['x'] . '"
                      y="' . $values['y'] . '"
                      width="' . $values['width'] . '"
                      height="' . $values['height'] . '"
                      data-value="' . $id . '">
                   <title>' . $display_definition->getTooltip() . '</title>
                </rect>';
    }

    /**
     * Decodes 'a:1;b:2'
     * to
     * [
     * 'a' => '1',
     * 'b' => '2'
     * ]
     * @param string $coordinates
     * @return array
     */
    private function decodeCoordinates(string $coordinates) : array
    {
        $raw_values = explode(';', $coordinates);

        $values = [];

        foreach ($raw_values as $raw_value) {
            $raw_split = explode(':', $raw_value);
            $values[$raw_split[0]] = $raw_split[1];
        }

        return $values;
    }

    /**
     * @param int $id
     * @return string
     */
    private function getClass(string $id) : string
    {
        $class = '';

        if (!is_null($this->answer) && in_array($id, $this->answer->getSelectedIds())) {
            $class .= ' selected';
        }

        if ($this->configuration->isMultipleChoice()) {
            $class .= ' multiple_choice';
        }

        return $class;
    }

    /**
     * @return Answer
     */
    public function readAnswer() : ?AbstractValueObject
    {
        $value = $this->readString($this->getPostName());

        if (empty($value)) {
            return null;
        }

        return MultipleChoiceAnswer::create(explode(',', $value));
    }

    /**
     * @return bool
     */
    public function isComplete() : bool
    {
        if (empty($this->configuration->getImage())) {
            return false;
        }

        if (is_null($this->question->getAnswerOptions()) || count($this->question->getAnswerOptions()->getOptions()) < 2) {
            return false;
        }

        foreach ($this->question->getAnswerOptions()->getOptions() as $option) {
            /** @var ImageMapEditorDefinition $option_config */
            $option_config = $option->getDisplayDefinition();

            if (empty($option_config->getType()) || empty($option_config->getCoordinates())) {
                return false;
            }
        }

        return true;
    }
}
