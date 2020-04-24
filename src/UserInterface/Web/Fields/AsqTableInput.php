<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Fields;

use Exception;
use ilNumberInputGUI;
use ilRadioGroupInputGUI;
use ilRadioOption;
use ilTemplate;
use ilTextInputGUI;
use srag\asq\Domain\Model\QuestionPlayConfiguration;
use srag\asq\UserInterface\Web\PathHelper;
use srag\asq\UserInterface\Web\AsqHtmlPurifier;

/**
 * Class AsqTableInput
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class AsqTableInput extends ilTextInputGUI {
    const OPTION_ORDER = 'TableInputOrder';
    const OPTION_HIDE_ADD_REMOVE = 'TableInputHideAddRemove';
    const OPTION_HIDE_EMPTY = 'TableInputHideEmpty';
    const OPTION_MIN_ROWS = 'TableInputMinRows';
    const DEFAULT_MIN_ROWS = 1;
    
    /**
     * @var AsqTableInputFieldDefinition[]
     */
    private $definitions;
    /**
     * @var array
     */
    protected $values;    
    /**
     * @var array
     */
    private $form_configuration;
    
    /**
     * @param string $title
     * @param string $post_var
     * @param array $values
     * @param array $definitions
     * @param array $form_configuration
     */
    public function __construct(string $title,
        string $post_var,
        array $values = [],
        array $definitions = [],
        array $form_configuration = [])
    {
        parent::__construct($title, $post_var);

        $this->definitions = $definitions;
        $this->form_configuration = $form_configuration;
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->readValues();
        } else {
            $this->values = $values;
        }
    }
    
    public function getMinRows() : int 
    {
        if(array_key_exists(self::OPTION_MIN_ROWS, $this->form_configuration)) {
            return $this->form_configuration[self::OPTION_MIN_ROWS];
        }
        
        return self::DEFAULT_MIN_ROWS;
    }
    
    /**
     * @param string $a_mode
     *
     * @return string
     * @throws \ilTemplateException
     */
    public function render($a_mode = '') {
        global $DIC;
        
        $tpl = new ilTemplate(PathHelper::getBasePath(__DIR__) . "templates/default/tpl.TableInput.html", true, true);
        
        /** @var AsqTableInputFieldDefinition $definition */
        foreach ($this->definitions as $definition) {
            $tpl->setCurrentBlock('header_entry');
            $tpl->setVariable('HEADER_TEXT', $definition->getHeader());
            $tpl->parseCurrentBlock();
        }
        
        if ($this->hasActions()) {     
            $tpl->setCurrentBlock('command_header');
            $tpl->setVariable('COMMANDS_TEXT', $DIC->language()->txt('asq_label_actions'));
            $tpl->parseCurrentBlock();
        }
        
        $row_id = 1;
        
        $empty = false;
        //add dummy object if no options are defined so that one empty line will be printed
        while (count($this->values) < $this->getMinRows()) {
            $this->values[] = null;
            $empty = true;
        }
        
        if ($empty && array_key_exists(self::OPTION_HIDE_EMPTY, $this->form_configuration)) {
            $tpl->touchBlock('hide');
        }
        
        foreach ($this->values as $value) {
            /** @var AsqTableInputFieldDefinition $definition */
            foreach ($this->definitions as $definition) {
                $tpl->setCurrentBlock('body_entry');
                $tpl->setVariable('ENTRY_CLASS', '');
                $tpl->setVariable('ENTRY', $this->generateField($definition, $row_id, $value[$definition->getPostVar()]));
                $tpl->parseCurrentBlock();
            }
            
            if ($this->hasActions()) {
                if (array_key_exists(self::OPTION_ORDER, $this->form_configuration)) {
                    $tpl->touchBlock('move');
                }
                
                if (!array_key_exists(self::OPTION_HIDE_ADD_REMOVE, $this->form_configuration)) {
                    $tpl->touchBlock('add');
                }
                
                if (!array_key_exists(self::OPTION_HIDE_ADD_REMOVE, $this->form_configuration)) {
                    $tpl->touchBlock('remove');
                }
                
                $tpl->touchBlock('command_row');
            }
            
            $tpl->setCurrentBlock('row');
            $tpl->setVariable('ID', $row_id);
            $tpl->parseCurrentBlock();
            
            $row_id += 1;
        }
        
        $tpl->setCurrentBlock('count');
        $tpl->setVariable('COUNT_POST_VAR', $this->getPostVar());
        $tpl->setVariable('COUNT', sizeof($this->values));
        $tpl->parseCurrentBlock();
        
        
        return $tpl->get();
    }
    
    /**
     * @return bool
     */
    private function hasActions() :bool {
        return array_key_exists(self::OPTION_ORDER, $this->form_configuration) ||
        !array_key_exists(self::OPTION_HIDE_ADD_REMOVE, $this->form_configuration);
        
    }
    
    /**
     * @return bool
     */
    public function checkInput() : bool {
        //TODO required etc.
        return true;
    }
    
    public static function readValuesFromPost($post_var, $definitions) {
        $count = intval($_POST[$post_var]);
        
        $values = [];
        for ($i = 1; $i <= $count; $i++) {
            $new_value = [];
            
            foreach ($definitions as $definition) {
                $item_post_var = self::getTableItemPostVar($i, $post_var, $definition->getPostVar());
                
                if (array_key_exists($item_post_var, $_POST)) {
                    $new_value[$definition->getPostVar()] =
                    AsqHtmlPurifier::getInstance()->purify(
                        $_POST[$item_post_var]);                    
                }

            }
            
            $values[] = $new_value;
        }
        
        return $values;
    }
    
    private static function getTableItemPostVar(int $id, string $postvar, string $definition_postvar) {
        return sprintf('%s_%s_%s', $id, $postvar, $definition_postvar);
    }
        
    
    /**
     * @param QuestionPlayConfiguration $play
     *
     * @return array
     */
    public function readValues() {
        $this->values = self::readValuesFromPost($this->getPostVar(), $this->definitions);
        return $this->values;
    }
    
    /**
     * @param array $values
     */
    public function setValues(array $values) {
        $this->values = $values;
    }
    
    /**
     * @param AsqTableInputFieldDefinition $definition
     * @param int                             $row_id
     * @param                                 $value
     *
     * @return string
     * @throws Exception
     */
    private function generateField(AsqTableInputFieldDefinition $definition, int $row_id, $value)
    {
        switch ($definition->getType()) {
            case AsqTableInputFieldDefinition::TYPE_TEXT:
                return $this->generateTextField(self::getTableItemPostVar($row_id, $this->getPostVar(), $definition->getPostVar()), $value);
            case AsqTableInputFieldDefinition::TYPE_TEXT_AREA:
                return $this->generateTextArea(self::getTableItemPostVar($row_id, $this->getPostVar(), $definition->getPostVar()), $value);
            case AsqTableInputFieldDefinition::TYPE_IMAGE:
                return $this->generateImageField(self::getTableItemPostVar($row_id, $this->getPostVar(), $definition->getPostVar()), $value);
            case AsqTableInputFieldDefinition::TYPE_NUMBER:
                return $this->generateNumberField(self::getTableItemPostVar($row_id, $this->getPostVar(), $definition->getPostVar()), $value);
            case AsqTableInputFieldDefinition::TYPE_RADIO:
                return $this->generateRadioField(self::getTableItemPostVar($row_id, $this->getPostVar(), $definition->getPostVar()), $value, $definition->getOptions());
            case AsqTableInputFieldDefinition::TYPE_DROPDOWN:
                return $this->generateDropDownField(self::getTableItemPostVar($row_id, $this->getPostVar(), $definition->getPostVar()), $value, $definition->getOptions());
            case AsqTableInputFieldDefinition::TYPE_BUTTON:
                return $this->generateButton(self::getTableItemPostVar($row_id, $this->getPostVar(), $definition->getPostVar()), $definition->getOptions());
            case AsqTableInputFieldDefinition::TYPE_HIDDEN:
                return $this->generateHiddenField(self::getTableItemPostVar($row_id, $this->getPostVar(), $definition->getPostVar()), $value ?? $definition->getOptions()[0]);
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
     * @return ilTextInputGUI
     */
    private function generateTextField(string $post_var, $value) {
        $field = new ilTextInputGUI('', $post_var);
        
        $this->setFieldValue($post_var, $value, $field);
        
        return $field->render();
    }
    
    private function generateTextArea(string $post_var, $value) {
        $tpl = new ilTemplate(PathHelper::getBasePath(__DIR__) . 'templates/default/tpl.TextAreaField.html', true, true);
        
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
     * @return AsqImageUpload
     */
    private function generateImageField(string $post_var, $value) {
        $field = new AsqImageUpload('', $post_var);
        
        $field->setImagePath($value);
        
        return $field->render();
    }
    
    /**
     * @param string $post_var
     * @param        $value
     *
     * @return ilNumberInputGUI
     */
    private function generateNumberField(string $post_var, $value) {
        $field = new ilNumberInputGUI('', $post_var);
        $field->setSize(2);
        
        $this->setFieldValue($post_var, $value, $field);
        
        return $field->render();
    }
    
    private function generateRadioField(string $post_var, $value, $options) {
        $field = new ilRadioGroupInputGUI('', $post_var);
        
        $this->setFieldValue($post_var, $value, $field);
        
        foreach ($options as $key=>$value)
        {
            $option = new ilRadioOption($key, $value);
            $field->addOption($option);
        }
        return $field->render();
    }
    
    private function generateDropDownField(string $post_var, $value, $options) {
        $field = new \ilSelectInputGUI('', $post_var);
        
        $field->setOptions($options);
        
        $this->setFieldValue($post_var, $value, $field);
        
        return $field->render();
    }
    
    private function generateButton(string $id, $options) {
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
    
    private function generateHiddenField(string $post_var, $value) {
        return sprintf('<input type="hidden" id="%1$s" name="%1$s" value="%2$s" />', $post_var, $value);
    }
    
    private function generateLabel($text, $name) {
        return sprintf('<span class="%s">%s</span>', $name,  $text);
    }
    
    /**
     * @param string $post_var
     * @param $value
     * @param $field
     */
    private function setFieldValue(string $post_var, $value, $field)
    {
        if (array_key_exists($post_var, $_POST)) {
            $field->setValue($_POST[$post_var]);
        }
        else if ($value !== null) {
            $field->setValue($value);
        }
    }
}