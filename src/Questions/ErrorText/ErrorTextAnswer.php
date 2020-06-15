<?php
declare(strict_types=1);

namespace srag\asq\Questions\ErrorText;

use srag\asq\Domain\Model\Answer\Answer;

/**
 * Class ErrorTextEditor
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class ErrorTextAnswer extends Answer
{
    /**
     * @var int[]
     */
    protected $selected_word_indexes;

    /**
     * @param array $selected_word_indexes
     * @return ErrorTextAnswer
     */
    public static function create(array $selected_word_indexes = []) : ErrorTextAnswer
    {
        $object = new ErrorTextAnswer();
        $object->selected_word_indexes = $selected_word_indexes;
        return $object;
    }

    /**
     * @return array
     */
    public function getSelectedWordIndexes() : array
    {
        return $this->selected_word_indexes;
    }

    /**
     * @return string
     */
    public function getPostString() : string
    {
        return implode(',', $this->selected_word_indexes);
    }
}