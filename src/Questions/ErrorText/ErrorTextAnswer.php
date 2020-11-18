<?php
declare(strict_types=1);

namespace srag\asq\Questions\ErrorText;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class ErrorTextEditor
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class ErrorTextAnswer extends AbstractValueObject
{
    /**
     * @var int[]
     */
    protected $selected_word_indexes;

    /**
     * @param array $selected_word_indexes
     */
    public function __construct(array $selected_word_indexes = [])
    {
        $this->selected_word_indexes = $selected_word_indexes;
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
