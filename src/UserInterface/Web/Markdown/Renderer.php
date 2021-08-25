<?php

namespace srag\asq\UserInterface\Web\Markdown;

use ILIAS\UI\Implementation\Render\AbstractComponentRenderer;
use ILIAS\UI\Implementation\Render\ResourceRegistry;
use ILIAS\UI\Renderer as RendererInterface;
use ILIAS\UI\Component;
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

    public function render(Component\Component $component, RendererInterface $default_renderer)
    {
        /**
         * @var Markdown $component
         */
        $this->checkComponent($component);

        $tpl = new ilTemplate($this->getBasePath(__DIR__) . 'templates/default/tpl.Markdown.html', true, true);
        $id = $this->bindJavaScript($component);

        $tpl->setCurrentBlock('content');
        $tpl->setVariable('ID', $id);
        $tpl->setVariable('CONTENT', $component->getContent());
        $tpl->parseCurrentBlock();

        return $tpl->get();
    }

    public function registerResources(ResourceRegistry $registry)
    {
        parent::registerResources($registry);

        $registry->register($this->getBasePath(__DIR__) . 'js/markdown.js');

        $registry->register($this->getBasePath(__DIR__) . 'css/toastui-editor.css');
        $registry->register($this->getBasePath(__DIR__) . 'js/toastui-editor-all.min.js');
    }

    protected function getComponentInterfaceName()
    {
        return [Markdown::class];
    }
}
