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

    /**
     * @var ?int
     */
    protected $number_of_requested_answers;

    /**
     * @param int $number_of_requested_answers
     *
     * @return TextSubsetEditorConfiguration
     */
    public static function create(?int $number_of_requested_answers = null)
    {
        $object = new TextSubsetEditorConfiguration();
        $object->number_of_requested_answers = $number_of_requested_answers;
        return $object;
    }

    /**
     * @return ?int
     */
    public function getNumberOfRequestedAnswers() : ?int
    {
        return $this->number_of_requested_answers;
    }
}
