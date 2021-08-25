<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Fields\Markdown;

use ILIAS\UI\Implementation\Render\ResourceRegistry;
use ILIAS\UI\Implementation\Render\Template;
use ILIAS\UI\Renderer as RendererInterface;
use ILIAS\UI\Component\Component;
use ILIAS\UI\Implementation\Render\AbstractComponentRenderer;
use ilTemplate;
use srag\asq\Infrastructure\Helpers\PathHelper;
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
    protected function renderInputField() : string
    {
        $tpl = new ilTemplate($this->getBasePath(__DIR__) . "templates/default/tpl.MarkdownInput.html", true, true);
        $tpl->setVariable("NAME", $this->component->getName());
        $id = $this->bindJavaScript($this->component);
        $tpl->setVariable("ID", $id);
        $tpl->setVariable("VALUE", $this->component->getValue());
        return $tpl->get();
    }

    public function registerResources(ResourceRegistry $registry)
    {
        parent::registerResources($registry);

        $registry->register($this->getBasePath(__DIR__) . 'js/markdown_field.js');

        $registry->register($this->getBasePath(__DIR__) . 'css/toastui-editor.css');
        $registry->register($this->getBasePath(__DIR__) . 'js/toastui-editor-all.min.js');
    }

    protected function getComponentInterfaceName() : array
    {
        return [Markdown::class];
    }
}
