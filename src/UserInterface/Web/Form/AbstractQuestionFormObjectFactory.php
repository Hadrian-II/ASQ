<?php

namespace srag\asq\UserInterface\Web\Form;

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
abstract class AbstractQuestionFormObjectFactory implements IQuestionFormObjectFactory
{
    /**
     * Reads float value from POST
     *
     * @param string $postvar
     * @return ?float
     */
    public static function readFloat(string $postvar) : ?float
    {
        if (! array_key_exists($postvar, $_POST) ||
            ! is_numeric($_POST[$postvar]))
        {
            return null;
        }

        return floatval($_POST[$postvar]);
    }

    /**
     * Reads int value from POST
     *
     * @param string $postvar
     * @return ?int
     */
    public static function readInt(string $postvar) : ?int
    {
        if (! array_key_exists($postvar, $_POST) ||
            ! is_numeric($_POST[$postvar]))
        {
            return null;
        }

        return intval($_POST[$postvar]);
    }
}