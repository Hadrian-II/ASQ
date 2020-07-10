<?php
declare(strict_types = 1);

namespace srag\asq\UserInterface\Web\Form;

use ilLanguage;

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
     * @param ilLanguage $language
     */
    public function __construct(ilLanguage $language)
    {
        $this->language = $language;
    }
}
