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
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
abstract class AbstractAnswerOptionFactory implements IAnswerOptionFactory
{
    use InputHandlingTrait;

    protected ilLanguage $language;

    protected Factory $factory;

    public function __construct(ilLanguage $language, UIServices $ui)
    {
        $this->language = $language;
        $this->factory = $ui->factory();
    }

    /**
     * Creates POST key for index and key name
     *
     * @param string $index
     * @param string $name
     * @return string
     */
    protected function getPostKey(string $index, string $name) : string
    {
        return sprintf('%s_answer_options_%s', $index, $name);
    }
}
