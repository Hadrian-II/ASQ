<?php
declare(strict_types=1);

namespace srag\asq\Questions\ErrorText;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class ErrorTextEditor
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class ErrorTextAnswer extends AbstractValueObject
{
    /**
     * @var int[]
     */
    protected array $selected_word_indexes;

    public function __construct(array $selected_word_indexes = [])
    {
        $this->selected_word_indexes = $selected_word_indexes;
    }

    /**
     * @return int[]
     */
    public function getSelectedWordIndexes() : array
    {
        return $this->selected_word_indexes;
    }

    public function getPostString() : string
    {
        return implode(',', $this->selected_word_indexes);
    }
}
