<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Fields\AsqImageUpload;

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
        $tpl = new ilTemplate($this->getBasePath(__DIR__) . "templates/default/tpl.image_upload.html", true, true);

        if (!empty($this->component->getValue())) {
            $tpl->setCurrentBlock('has_image');
            $tpl->setVariable('NAME', $this->component->getName());
            $tpl->setVariable('VALUE', $this->component->getValue());
            $tpl->setVariable('TXT_DELETE', $this->txt("delete_existing_file"));
            $tpl->parseCurrentBlock();
        }

        $tpl->setCurrentBlock('image_upload');
        $tpl->setVariable('NAME', $this->component->getName());
        $tpl->setVariable('VALUE', $this->component->getValue());
        $tpl->parseCurrentBlock();

        return $tpl->get();
    }

    protected function getComponentInterfaceName() : array
    {
        return [AsqImageUpload::class];
    }
}
