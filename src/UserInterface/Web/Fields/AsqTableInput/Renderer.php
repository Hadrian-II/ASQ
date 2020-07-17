<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Fields\AsqTableInput;

use Exception;
use ilNumberInputGUI;
use ilRadioGroupInputGUI;
use ilRadioOption;
use ilTemplate;
use ilTextInputGUI;
use ilSelectInputGUI;
use srag\asq\PathHelper;
use srag\asq\Domain\Model\Configuration\QuestionPlayConfiguration;
use srag\asq\UserInterface\Web\Fields\AsqImageUpload;
use srag\asq\UserInterface\Web\PostAccess;
use ILIAS\UI\Component\Input\Field\Input;
use ILIAS\UI\Implementation\Render\AbstractComponentRenderer;
use ILIAS\UI\Implementation\Render\Template;
use ILIAS\UI\Renderer as RendererInterface;
use ILIAS\UI\Component;

/**
 * Class AsqTableInput
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class Renderer extends AbstractComponentRenderer
{
    use PathHelper;
    use PostAccess;

    /**
     * @var AsqTableInput
     */
    private $component;

    //TODO stole method from Input/Field/Renderer, see to integrate this into input field renderer
    /**
     * {@inheritDoc}
     * @see \ILIAS\UI\Implementation\Render\ComponentRenderer::render()
     */
    public function render(Component\Component $input, RendererInterface $default_renderer) : string
    {
        $this->component = $input;

        $tpl = new ilTemplate("src/UI/templates/default/Input/tpl.context_form.html", true, true);
        /**
         * TODO: should we throw an error in case for no name or render without name?
         *
         * if(!$input->getName()){
         * throw new \LogicException("Cannot render '".get_class($input)."' no input name given.
         * Is there a name source attached (is this input packed into a container attaching
         * a name source)?");
         * } */
        if ($input->getName()) {
            $tpl->setVariable("NAME", $input->getName());
        } else {
            $tpl->setVariable("NAME", "");
        }

        $tpl->setVariable("LABEL", $input->getLabel());
        $tpl->setVariable("INPUT", $this->renderInputField());

        if ($input->getByline() !== null) {
            $tpl->setCurrentBlock("byline");
            $tpl->setVariable("BYLINE", $input->getByline());
            $tpl->parseCurrentBlock();
        }

        if ($input->isRequired()) {
            $tpl->touchBlock("required");
        }

        if ($input->getError() !== null) {
            $tpl->setCurrentBlock("error");
            $tpl->setVariable("ERROR", $input->getError());
            $tpl->parseCurrentBlock();
        }

        return $tpl->get();
    }

    /**
     * @param string $a_mode
     *
     * @return string
     * @throws \ilTemplateException
     */
    private function renderInputField() : string
    {
        $values = $this->component->getValue() ?? [];

        $tpl = new ilTemplate($this->getBasePath(__DIR__) . "templates/default/tpl.TableInput.html", true, true);

        /** @var AsqTableInputFieldDefinition $definition */
        foreach ($this->component->getDefinitions() as $definition) {
            $tpl->setCurrentBlock('header_entry');
            $tpl->setVariable('HEADER_TEXT', $definition->getHeader());
            $tpl->parseCurrentBlock();
        }

        if ($this->hasActions()) {
            $tpl->setCurrentBlock('command_header');
            $tpl->setVariable('COMMANDS_TEXT', $this->txt('asq_label_actions'));
            $tpl->parseCurrentBlock();
        }

        $row_id = 1;

        $empty = false;

        //add dummy object if no options are defined so that one empty line will be printed
        while (count($values) < $this->getMinRows()) {
            $values[] = null;
            $empty = true;
        }

        if ($empty && array_key_exists(AsqTableInput::OPTION_HIDE_EMPTY, $this->component->getOptions())) {
            $tpl->touchBlock('hide');
        }

        foreach ($values as $value) {
            /** @var AsqTableInputFieldDefinition $definition */
            foreach ($this->component->getDefinitions() as $definition) {
                $tpl->setCurrentBlock('body_entry');
                $tpl->setVariable('ENTRY_CLASS', '');
                $tpl->setVariable('ENTRY', $this->generateField($definition, $row_id, $value[$definition->getPostVar()]));
                $tpl->parseCurrentBlock();
            }

            if ($this->hasActions()) {
                if (array_key_exists(AsqTableInput::OPTION_ORDER, $this->component->getOptions())) {
                    $tpl->touchBlock('move');
                }

                if (!array_key_exists(AsqTableInput::OPTION_HIDE_ADD_REMOVE, $this->component->getOptions())) {
                    $tpl->touchBlock('add');
                }

                if (!array_key_exists(AsqTableInput::OPTION_HIDE_ADD_REMOVE, $this->component->getOptions())) {
                    $tpl->touchBlock('remove');
                }

                $tpl->touchBlock('command_row');
            }

            $tpl->setCurrentBlock('row');
            $tpl->setVariable('ID', $row_id);
            $tpl->parseCurrentBlock();

            $row_id += 1;
        }

        return $tpl->get();
    }

    /**
     * @return int
     */
    public function getMinRows() : int
    {
        if (array_key_exists(AsqTableInput::OPTION_MIN_ROWS, $this->component->getOptions())) {
            return $this->component->getOptions()[AsqTableInput::OPTION_MIN_ROWS];
        }

        return AsqTableInput::DEFAULT_MIN_ROWS;
    }

    /**
     * @return bool
     */
    private function hasActions() : bool
    {
        return array_key_exists(AsqTableInput::OPTION_ORDER, $this->component->getOptions()) ||
        !array_key_exists(AsqTableInput::OPTION_HIDE_ADD_REMOVE, $this->component->getOptions());
    }

    /**
     * @param int $id
     * @param string $postvar
     * @param string $definition_postvar
     * @return string
     */
    private function getTableItemPostVar(int $id, string $definition_postvar) : string
    {
        return sprintf('%s_%s_%s', $id, $this->component->getName(), $definition_postvar);
    }


    /**
     * @param QuestionPlayConfiguration $play
     *
     * @return array
     */
    public function readValues() : array
    {
        $count = intval($this->getPostValue($this->getPostVar()));

        $values = [];
        for ($i = 1; $i <= $count; $i++) {
            $new_value = [];

            foreach ($this->component->getDefinitions() as $definition) {
                $item_post_var = AsqTableInput::getTableItemPostVar($i, $this->getPostVar(), $definition->getPostVar());

                if ($this->isPostVarSet($item_post_var)) {
                    $new_value[$definition->getPostVar()] = $this->getPostValue($item_post_var);
                }
            }

            $values[] = $new_value;
        }

        return $values;
    }

    /**
     * @param AsqTableInputFieldDefinition $definition
     * @param int                             $row_id
     * @param                                 $value
     *
     * @return string
     * @throws Exception
     */
    private function generateField(AsqTableInputFieldDefinition $definition, int $row_id, $value) : string
    {
        switch ($definition->getType()) {
            case AsqTableInputFieldDefinition::TYPE_TEXT:
                return $this->generateTextField($this->getTableItemPostVar($row_id, $definition->getPostVar()), $value);
            case AsqTableInputFieldDefinition::TYPE_TEXT_AREA:
                return $this->generateTextArea($this->getTableItemPostVar($row_id, $definition->getPostVar()), $value);
            case AsqTableInputFieldDefinition::TYPE_IMAGE:
                return $this->generateImageField($this->getTableItemPostVar($row_id, $definition->getPostVar()), $value);
            case AsqTableInputFieldDefinition::TYPE_NUMBER:
                return $this->generateNumberField($this->getTableItemPostVar($row_id, $definition->getPostVar()), $value);
            case AsqTableInputFieldDefinition::TYPE_RADIO:
                return $this->generateRadioField($this->getTableItemPostVar($row_id, $definition->getPostVar()), $value, $definition->getOptions());
            case AsqTableInputFieldDefinition::TYPE_DROPDOWN:
                return $this->generateDropDownField($this->getTableItemPostVar($row_id, $definition->getPostVar()), $value, $definition->getOptions());
            case AsqTableInputFieldDefinition::TYPE_BUTTON:
                return $this->generateButton($this->getTableItemPostVar($row_id, $definition->getPostVar()), $definition->getOptions());
            case AsqTableInputFieldDefinition::TYPE_HIDDEN:
                return $this->generateHiddenField($this->getTableItemPostVar($row_id, $definition->getPostVar()), $value ?? $definition->getOptions()[0]);
            case AsqTableInputFieldDefinition::TYPE_LABEL:
                return $this->generateLabel($value, $definition->getPostVar());
            default:
                throw new Exception('Please implement all fieldtypes you define');
        }
    }

    /**
     * @param string $post_var
     * @param        $value
     *
     * @return string
     */
    private function generateTextField(string $post_var, $value) : string
    {
        $field = new ilTextInputGUI('', $post_var);

        $field->setValue($value);

        return $field->render();
    }

    /**
     * @param string $post_var
     * @param $value
     * @return string
     */
    private function generateTextArea(string $post_var, $value) : string
    {
        $tpl = new ilTemplate($this->getBasePath(__DIR__) . 'templates/default/tpl.TextAreaField.html', true, true);

        $tpl->setCurrentBlock('textarea');
        $tpl->setVariable('POST_NAME', $post_var);
        $tpl->setVariable('VALUE', $value);
        $tpl->parseCurrentBlock();

        return $tpl->get();
    }

    /**
     * @param string $post_var
     * @param        $value
     *
     * @return string
     */
    private function generateImageField(string $post_var, $value) : string
    {
        $field = new AsqImageUpload('', $post_var);

        $field->setImagePath($value);

        return $field->render();
    }

    /**
     * @param string $post_var
     * @param        $value
     *
     * @return string
     */
    private function generateNumberField(string $post_var, $value) : string
    {
        $field = new ilNumberInputGUI('', $post_var);
        $field->setSize(2);

        $field->setValue($value);

        return $field->render();
    }

    /**
     * @param string $post_var
     * @param $value
     * @param $options
     * @return string
     */
    private function generateRadioField(string $post_var, $value, $options) : string
    {
        $field = new ilRadioGroupInputGUI('', $post_var);

        $field->setValue($value);

        foreach ($options as $key => $value) {
            $option = new ilRadioOption($key, $value);
            $field->addOption($option);
        }
        return $field->render();
    }

    /**
     * @param string $post_var
     * @param $value
     * @param $options
     * @return string
     */
    private function generateDropDownField(string $post_var, $value, $options) : string
    {
        $field = new ilSelectInputGUI('', $post_var);

        $field->setOptions($options);

        $field->setValue($value);

        return $field->render();
    }

    /**
     * @param string $id
     * @param $options
     * @return string
     */
    private function generateButton(string $id, $options) : string
    {
        $css = 'btn btn-default';
        if (array_key_exists('css', $options)) {
            $css .= ' ' . $options['css'];
        }

        $title = '';
        if (array_key_exists('title', $options)) {
            $title .= ' ' . $options['title'];
        }

        return sprintf('<input type="Button" id="%s" class="%s" value="%s" />', $id, $css, $title);
    }

    /**
     * @param string $post_var
     * @param $value
     * @return string
     */
    private function generateHiddenField(string $post_var, $value) : string
    {
        return sprintf('<input type="hidden" id="%1$s" name="%1$s" value="%2$s" />', $post_var, $value);
    }

    /**
     * @param $text
     * @param $name
     * @return string
     */
    private function generateLabel($text, $name) : string
    {
        return sprintf('<span class="%s">%s</span>', $name, $text);
    }

    protected function getComponentInterfaceName()
    {
        return [AsqTableInput::class];
    }
}
