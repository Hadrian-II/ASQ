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
abstract class AbstractAnswerOptionFactory implements IAnswerOptionFactory
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

    /**
     *
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