<?php
declare(strict_types=1);

namespace srag\asq\Questions\Choice\Form\Editor\ImageMap\ImageFormPopup;

use ILIAS\UI\Renderer as RendererInterface;
use ILIAS\UI\Component\Component;
use ILIAS\UI\Implementation\Render\AbstractComponentRenderer;
use ilTemplate;
use srag\asq\Infrastructure\Helpers\PathHelper;

/**
 * Class Renderer
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class Renderer extends AbstractComponentRenderer
{
    use PathHelper;

    public function render(Component $input, RendererInterface $default_renderer) : string
    {
        $tpl = new ilTemplate($this->getBasePath(__DIR__) . 'templates/default/tpl.ImageMapEditorFormPopUp.html', true, true);
        $tpl->setVariable('POPUP_TITLE', $this->txt('asq_imagemap_popup_title'));
        $tpl->setVariable('IMAGE_SRC', $input->getValue());
        $tpl->setVariable('OK', $this->txt('ok'));
        $tpl->setVariable('CANCEL', $this->txt('cancel'));
        return $tpl->get();
    }

    protected function getComponentInterfaceName() : array
    {
        return [ImageFormPopup::class];
    }
}
