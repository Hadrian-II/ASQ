<?php
declare(strict_types=1);

namespace srag\asq\Questions\Choice\Form\Editor\ImageMap\ImageFormPopup;

use ILIAS\UI\Renderer as RendererInterface;
use ILIAS\UI\Component\Component;
use ILIAS\UI\Implementation\Render\AbstractComponentRenderer;
use ilTemplate;
use srag\asq\PathHelper;

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

    /**
     * {@inheritDoc}
     * @see \ILIAS\UI\Implementation\Render\ComponentRenderer::render()
     */
    public function render(Component $input, RendererInterface $default_renderer) : string
    {
        $tpl = new ilTemplate($this->getBasePath(__DIR__) . 'templates/default/tpl.ImageMapEditorFormPopUp.html', true, true);
        $tpl->setVariable('POPUP_TITLE', $this->txt('asq_imagemap_popup_title'));
        $tpl->setVariable('IMAGE_SRC', $input->getValue());
        $tpl->setVariable('OK', $this->txt('ok'));
        $tpl->setVariable('CANCEL', $this->txt('cancel'));
        return $tpl->get();
    }

    protected function getComponentInterfaceName()
    {
        return [ImageFormPopup::class];
    }
}
