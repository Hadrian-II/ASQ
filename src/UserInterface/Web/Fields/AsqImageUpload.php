<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Fields;

use ilFileInputGUI;
use srag\asq\UserInterface\Web\PostAccess;

/**
 * Class AsqImageUpload
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class AsqImageUpload extends ilFileInputGUI
{
    use PostAccess;

    /**
     * @var string
     */
    private $image_path;

    /**
     * Constructor
     *
     * @param	string	$a_title	Title
     * @param	string	$a_postvar	Post Variable
     */
    public function __construct($a_title = "", $a_postvar = "")
    {
        parent::__construct($a_title, $a_postvar);
        $this->setType("image_file");
        $this->setSuffixes(array("jpg", "jpeg", "png", "gif", "svg"));
    }

    /**
     * Set Value. (used for displaying file title of existing file below input field)
     *
     * @param	string	$a_value	Value
     */
    public function setImagePath($a_value)
    {
        $this->image_path = $a_value;

        if (!empty($a_value)) {
            parent::setValue(' ');
        } else {
            parent::setValue('');
        }
    }

    /**
     * Get Value.
     *
     * @return	string	Value
     */
    public function getImagePath()
    {
        return $this->image;
    }

    /**
     * @return	boolean		Input ok, true/false
     */
    public function checkInput()
    {
        $post = $this->getPostValue($this->getPostVar());

        $value = parent::checkInput();

        /* $_POST reference needed as parent destroys input */
        $_POST[$this->getPostVar()] = $post;

        return $value;
    }

    /**
     * Render html
     */
    public function render($a_mode = "")
    {
        //TODO create template when definitive
        $additional = '<input type="hidden" name="' . $this->getPostVar() . '" value="' . $this->image_path . '" />';
        $delete = '';

        if (!empty($this->image_path)) {
            $additional .= '<img class="image_preview" style="margin: 5px 0px 5px 0px; max-width: 333px;" src="' . $this->image_path . '" border="0" /><br />';

            if (!$this->required) {
                $delete = '<div class="checkbox">
                        <label>
                            <input type="checkbox"
                                   name="' . $this->getPostVar() . '_delete"
                                   id="' . $this->getPostVar() . '_delete"
                                   value="1" />' .
                                   $this->lng->txt("delete_existing_file") .
                                   '</label>
                       </div>';
            }
        }

        if ($this->getDisabled()) {
            return $additional;
        } else {
            return '<div style="width: 333px;">' . parent::render($a_mode) . $additional . $delete . '</div>';
        }
    }
}
