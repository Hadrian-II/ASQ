<?php
declare(strict_types = 1);

namespace srag\asq\UserInterface\Web\Form\Factory;

use ILIAS\DI\UIServices;
use ILIAS\UI\Factory;
use ilLanguage;
use srag\asq\UserInterface\Web\Form\InputHandlingTrait;
use srag\asq\Application\Service\UIService;

/**
 * Abstract Class AbstractQuestionFormFactory
 *
 * Contains Methods that are needed for a FormFactory to Work
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
abstract class AbstractObjectFactory implements IObjectFactory
{
    use InputHandlingTrait;

    protected ilLanguage $language;

    protected Factory $factory;

    protected UIService $asq_ui;

    public function __construct(ilLanguage $language, UIServices $ui, UIService $asq_ui)
    {
        $this->language = $language;
        $this->factory = $ui->factory();
        $this->asq_ui = $asq_ui;
    }
}
