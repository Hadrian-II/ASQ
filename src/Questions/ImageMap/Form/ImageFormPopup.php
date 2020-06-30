<?php
declare(strict_types=1);

namespace srag\asq\Questions\ImageMap\Form;

use ilTemplate;
use ilTextInputGUI;
use srag\asq\UserInterface\Web\PathHelper;
use ilLanguage;

/**
 * Class ImageFormPopup
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class ImageFormPopup extends ilTextInputGUI
{
    use PathHelper;

    /**
     * @var ilLanguage;
     */
    private $language;

    public function __construct(ilLanguage $language)
    {
        $this->language = $language;
    }

    /**
     * @param string $a_mode
     *
     * @return string
     * @throws \ilTemplateException
     */
    public function render($a_mode = '')
    {
        $tpl = new ilTemplate($this->getBasePath(__DIR__) . 'templates/default/tpl.ImageMapEditorFormPopUp.html', true, true);
        $tpl->setVariable('POPUP_TITLE', $this->language->txt('asq_imagemap_popup_title'));
        $tpl->setVariable('IMAGE_SRC', $this->getValue());
        $tpl->setVariable('OK', $this->language->txt('ok'));
        $tpl->setVariable('CANCEL', $this->language->txt('cancel'));
        return $tpl->get();
    }

    public function setValueByArray($values)
    {
        //do nothing as it has no post value and setvaluebypost resets value
    }
}
