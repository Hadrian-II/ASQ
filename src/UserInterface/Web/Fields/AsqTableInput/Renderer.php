<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Fields\AsqTableInput;

use ILIAS\UI\Renderer as RendererInterface;
use ILIAS\UI\Component\Component;
use ILIAS\UI\Implementation\Render\AbstractComponentRenderer;
use Exception;
use ilRadioGroupInputGUI;
use ilRadioOption;
use ilSelectInputGUI;
use ilTemplate;
use srag\asq\Infrastructure\Helpers\PathHelper;

/**
 * Class Renderer
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
    use AsqTablePostTrait;

    /**
     * @var AsqTableInput
     */
    private $component;

    //TODO stole method from Input/Field/Renderer, see to integrate this into input field renderer
    /**
     * {@inheritDoc}
     * @see \ILIAS\UI\Implementation\Render\ComponentRenderer::render()
     */
    public function render(Component $input, RendererInterface $default_renderer) : string
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

        if ($this->component->getOnLoadCode() !== null) {
            $id = $this->bindJavaScript($this->component);
            $tpl->setCurrentBlock('id');
            $tpl->setVariable('ID', $id);
            $tpl->parseCurrentBlock();
        }

        $tpl->setCurrentBlock('name');
        $tpl->setVariable('NAME', $this->component->getName());
        $tpl->parseCurrentBlock();

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
     * @param AsqTableInputFieldDefinition $definition
     * @param int                             $row_id
     * @param                                 $value
     *
     * @return string
     * @throws Exception
     */
    private function generateField(AsqTableInputFieldDefinition $definition, int $row_id, $value) : string
    {
        $name = $this->component->getName() ?? '';

        switch ($definition->getType()) {
            case AsqTableInputFieldDefinition::TYPE_TEXT:
                return $this->generateTextField($this->getTableItemPostVar($row_id, $name, $definition->getPostVar()), $value, $definition->getOptions());
            case AsqTableInputFieldDefinition::TYPE_TEXT_AREA:
                return $this->generateTextArea($this->getTableItemPostVar($row_id, $name, $definition->getPostVar()), $value);
            case AsqTableInputFieldDefinition::TYPE_IMAGE:
                return $this->generateImageField($this->getTableItemPostVar($row_id, $name, $definition->getPostVar()), $value);
            case AsqTableInputFieldDefinition::TYPE_NUMBER:
                return $this->generateNumberField($this->getTableItemPostVar($row_id, $name, $definition->getPostVar()), $value);
            case AsqTableInputFieldDefinition::TYPE_RADIO:
                return $this->generateRadioField($this->getTableItemPostVar($row_id, $name, $definition->getPostVar()), $value, $definition->getOptions());
            case AsqTableInputFieldDefinition::TYPE_DROPDOWN:
                return $this->generateDropDownField($this->getTableItemPostVar($row_id, $name, $definition->getPostVar()), $value, $definition->getOptions());
            case AsqTableInputFieldDefinition::TYPE_BUTTON:
                return $this->generateButton($this->getTableItemPostVar($row_id, $name, $definition->getPostVar()), $definition->getOptions());
            case AsqTableInputFieldDefinition::TYPE_HIDDEN:
                return $this->generateHiddenField($this->getTableItemPostVar($row_id, $name, $definition->getPostVar()), $value ?? $definition->getOptions()[0]);
            case AsqTableInputFieldDefinition::TYPE_LABEL:
                return $this->generateLabel($value, $definition->getPostVar());
            case AsqTableInputFieldDefinition::TYPE_CHECKBOX:
                return $this->generateCheckbox($this->getTableItemPostVar($row_id, $name, $definition->getPostVar()), $value);
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
    private function generateTextField(string $post_var, $value, ?array $options) : string
    {
        return sprintf(
            '<input type="text" id="%1$s" name="%1$s" class="form-control" value="%2$s" %3$s />',
            $post_var,
            $value,
            array_key_exists(AsqTableInputFieldDefinition::OPTION_MAX_LENGTH, $options ?? []) ?
                sprintf('maxlength="%s"', $options[AsqTableInputFieldDefinition::OPTION_MAX_LENGTH]) : ''
        );
    }

    /**
     * @param string $post_var
     * @param $value
     * @return string
     */
    private function generateTextArea(string $post_var, $value) : string
    {
        return sprintf('<textarea maxlength="200" id="%1$s" name="%1$s" class="form-control form-control-sm">%2$s</textarea>', $post_var, $value);
    }

    /**
     * @param string $post_var
     * @param $value
     * @return string
     */
    private function generateCheckbox(string $post_var, $value) : string
    {
        return sprintf(
            '<input type="checkbox" id="%1$s" name="%1$s" class="form-control form-control-sm" %2$s/',
            $post_var,
            $value === true ? 'checked="checked"' : ''
        );
    }

    /**
     * @param string $post_var
     * @param        $value
     *
     * @return string
     */
    private function generateImageField(string $post_var, $value) : string
    {
        $tpl = new ilTemplate($this->getBasePath(__DIR__) . "templates/default/tpl.image_upload.html", true, true);

        if (!empty($value)) {
            $tpl->setCurrentBlock('has_image');
            $tpl->setVariable('NAME', $post_var);
            $tpl->setVariable('VALUE', $value);
            $tpl->setVariable('TXT_DELETE', $this->txt("delete_existing_file"));
            $tpl->parseCurrentBlock();
        }

        $tpl->setCurrentBlock('image_upload');
        $tpl->setVariable('NAME', $post_var);
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
    private function generateNumberField(string $post_var, $value) : string
    {
        return sprintf('<input type="text" size="2" style="text-align: right;" maxlength="200" id="%1$s" name="%1$s" class="form-control" value="%2$s" />', $post_var, $value);
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
