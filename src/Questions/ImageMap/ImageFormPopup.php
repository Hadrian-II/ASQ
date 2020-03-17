<?php

namespace ILIAS\AssessmentQuestion\Questions\ImageMap;

use ILIAS\AssessmentQuestion\UserInterface\Web\PathHelper;
use ilTemplate;
use ilTextInputGUI;

/**
 * Class ImageFormPopup
 *
 * @package ILIAS\AssessmentQuestion\Authoring\DomainModel\Question\Answer\Option;
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 * @author  Björn Heyser <bh@bjoernheyser.de>
 * @author  Martin Studer <ms@studer-raimann.ch>
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 */
class ImageFormPopup extends ilTextInputGUI {
    /**
     * @param string $a_mode
     *
     * @return string
     * @throws \ilTemplateException
     */
    public function render($a_mode = '') {
        global $DIC;
        
        $tpl = new ilTemplate(PathHelper::getBasePath(__DIR__) . 'templates/default/tpl.ImageMapEditorFormPopUp.html', true, true);
        $tpl->setVariable('POPUP_TITLE', $DIC->language()->txt('asq_imagemap_popup_title'));
        $tpl->setVariable('IMAGE_SRC', $this->getValue());
        $tpl->setVariable('OK', $DIC->language()->txt('ok'));
        $tpl->setVariable('CANCEL', $DIC->language()->txt('cancel'));
        return $tpl->get();
    }
    
    public function setValueByArray($values) {
        //do nothing as it has no post value and setvaluebypost resets value
    }
}