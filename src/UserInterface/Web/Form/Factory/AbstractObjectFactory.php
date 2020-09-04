<?php
declare(strict_types = 1);

namespace srag\asq\UserInterface\Web\Form\Factory;

use ILIAS\DI\UIServices;
use ILIAS\UI\Factory;
use ilLanguage;
use srag\asq\UserInterface\Web\Form\InputHandlingTrait;

/**
 * Abstract Class AbstractQuestionFormFactory
 *
 * Contains Methods that are needed for a FormFactory to Work
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
abstract class AbstractObjectFactory implements IObjectFactory
{
    use InputHandlingTrait;

    /**
     * @var ilLanguage
     */
    protected $language;

    /**
     * @var Factory
     */
    protected $factory;

    /**
     * @param ilLanguage $language
     * @param UIServices $ui
     */
    public function __construct(ilLanguage $language, UIServices $ui)
    {
        $this->language = $language;
        $this->factory = $ui->factory();
    }
}
