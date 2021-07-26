<?php
declare(strict_types = 1);

namespace srag\asq\Questions\TextSubset\Editor\Data;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class TextSubsetEditorConfiguration
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 * @package srag/asq
 * @author Adrian Lüthi <al@studer-raimann.ch>
 */
class TextSubsetEditorConfiguration extends AbstractValueObject
{
    protected ?int $number_of_requested_answers;

    public function __construct(?int $number_of_requested_answers = null)
    {
        $this->number_of_requested_answers = $number_of_requested_answers;
    }

    public function getNumberOfRequestedAnswers() : ?int
    {
        return $this->number_of_requested_answers;
    }
}
