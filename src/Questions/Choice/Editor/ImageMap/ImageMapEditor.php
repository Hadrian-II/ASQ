<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Choice\Editor\ImageMap;

use Exception;
use ilTemplate;
use Fluxlabs\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\Answer\Option\AnswerOption;
use srag\asq\Infrastructure\Helpers\PathHelper;
use srag\asq\Questions\Choice\MultipleChoiceAnswer;
use srag\asq\Questions\Choice\Editor\ImageMap\Data\ImageMapEditorConfiguration;
use srag\asq\Questions\Choice\Editor\ImageMap\Data\ImageMapEditorDefinition;
use srag\asq\UserInterface\Web\PostAccess;
use srag\asq\UserInterface\Web\Component\Editor\AbstractEditor;

/**
 * Class ImageMapEditor
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class ImageMapEditor extends AbstractEditor
{
    use PostAccess;
    use PathHelper;

    private ImageMapEditorConfiguration $configuration;

    public function __construct(QuestionDto $question)
    {
        $this->configuration = $question->getPlayConfiguration()->getEditorConfiguration();

        parent::__construct($question);
    }

    public function additionalJSFile() : ?string
    {
        return $this->getBasePath(__DIR__) . 'src/Questions/Choice/Editor/ImageMap/ImageMapEditor.js';
    }

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
        foreach ($this->question->getAnswerOptions() as $answer_option) {
            /** @var ImageMapEditorDefinition $display_definition */
            $display_definition = $answer_option->getDisplayDefinition();

            $tpl->setCurrentBlock('answer_option');
            $tpl->setVariable('OPTION_SHAPE', $this->generateShape($display_definition, $answer_option->getOptionId()));
            $tpl->parseCurrentBlock();
        }

        return $tpl->get();
    }

    private function getPostName() : string
    {
        return $this->question->getId()->toString();
    }

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

    private function generatePolygon(ImageMapEditorDefinition $display_definition, string $id) : string
    {
        $values = $this->decodeCoordinates($display_definition->getCoordinates());

        return '<polygon class="' . $this->getClass($id) . '" points="' . $values['points'] . '" data-value="' . $id . '">
                   <title>' . $display_definition->getTooltip() . '</title>
                </polygon>';
    }

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

    public function readAnswer() : ?AbstractValueObject
    {
        $post_name = $this->getPostName();

        if (!$this->isPostVarSet($post_name)) {
            return null;
        }

        return new MultipleChoiceAnswer(explode(',', $this->getPostValue($post_name)));
    }

    public function isComplete() : bool
    {
        if (empty($this->configuration->getImage())) {
            return false;
        }

        if (is_null($this->question->getAnswerOptions()) || count($this->question->getAnswerOptions()) < 2) {
            return false;
        }

        foreach ($this->question->getAnswerOptions() as $option) {
            /** @var ImageMapEditorDefinition $option_config */
            $option_config = $option->getDisplayDefinition();

            if (empty($option_config->getType()) || empty($option_config->getCoordinates())) {
                return false;
            }
        }

        return true;
    }
}
