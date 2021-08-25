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
use ILIAS\UI\Implementation\Render\ResourceRegistry;
use srag\asq\UserInterface\Web\Fields\AsqFieldRenderer;

/**
 * Class Renderer
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class Renderer extends AsqFieldRenderer
{
    use AsqTablePostTrait;

    protected function renderInputField() : string
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

    public function getMinRows() : int
    {
        if (array_key_exists(AsqTableInput::OPTION_MIN_ROWS, $this->component->getOptions())) {
            return $this->component->getOptions()[AsqTableInput::OPTION_MIN_ROWS];
        }

        return AsqTableInput::DEFAULT_MIN_ROWS;
    }

    private function hasActions() : bool
    {
        return array_key_exists(AsqTableInput::OPTION_ORDER, $this->component->getOptions()) ||
        !array_key_exists(AsqTableInput::OPTION_HIDE_ADD_REMOVE, $this->component->getOptions());
    }

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
            case AsqTableInputFieldDefinition::TYPE_REALTEXT:
                return $this->generateRealTextField($this->getTableItemPostVar($row_id, $name, $definition->getPostVar()), $value);
            default:
                throw new Exception('Please implement all fieldtypes you define');
        }
    }

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

    private function generateTextArea(string $post_var, $value) : string
    {
        return sprintf('<textarea maxlength="200" id="%1$s" name="%1$s" class="form-control form-control-sm">%2$s</textarea>', $post_var, $value);
    }

    private function generateRealTextField(string $post_var, $value) : string
    {
        return sprintf('<div id="%1$s_editor" class="rte_field"></div>
                        <input type="hidden" id="%1$s" name="%1$s" value="%2$s" />',
                        $post_var,
                        $value);
    }

    private function generateCheckbox(string $post_var, $value) : string
    {
        return sprintf(
            '<input type="checkbox" id="%1$s" name="%1$s" class="form-control form-control-sm" %2$s/',
            $post_var,
            $value === true ? 'checked="checked"' : ''
        );
    }

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

    private function generateNumberField(string $post_var, $value) : string
    {
        return sprintf('<input type="text" size="2" style="text-align: right;" maxlength="200" id="%1$s" name="%1$s" class="form-control" value="%2$s" />', $post_var, $value);
    }

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

    private function generateDropDownField(string $post_var, $value, $options) : string
    {
        $field = new ilSelectInputGUI('', $post_var);

        $field->setOptions($options);

        $field->setValue($value);

        return $field->render();
    }

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

    private function generateHiddenField(string $post_var, $value) : string
    {
        return sprintf('<input type="hidden" id="%1$s" name="%1$s" value="%2$s" />', $post_var, $value);
    }

    private function generateLabel($text, $name) : string
    {
        return sprintf('<span class="%s">%s</span>', $name, $text);
    }

    public function registerResources(ResourceRegistry $registry) : void
    {
        parent::registerResources($registry);
        $registry->register($this->getBasePath(__DIR__) . 'js/table.js');

        $registry->register($this->getBasePath(__DIR__) . 'js/markdown_field.js');

        $registry->register($this->getBasePath(__DIR__) . 'css/toastui-editor.css');
        $registry->register($this->getBasePath(__DIR__) . 'js/toastui-editor-all.min.js');
    }

    protected function getComponentInterfaceName() : array
    {
        return [AsqTableInput::class];
    }
}
