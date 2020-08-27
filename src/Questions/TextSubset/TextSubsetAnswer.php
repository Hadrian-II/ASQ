<?php
declare(strict_types = 1);
namespace srag\asq\Questions\TextSubset;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class TextSubsetAnswer
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 * @package srag/asq
 * @author Adrian Lüthi <al@studer-raimann.ch>
 */
class TextSubsetAnswer extends AbstractValueObject
{

    /**
     * @var ?int[]
     */
    protected $answers;

    /**
     * @param array $answers
     * @return TextSubsetAnswer
     */
    public static function create(?array $answers = null) : TextSubsetAnswer
    {
        $object = new TextSubsetAnswer();
        $object->answers = $answers;
        return $object;
    }

    /**
     * @return ?array
     */
    public function getAnswers() : ?array
    {
        return $this->answers;
    }
}
