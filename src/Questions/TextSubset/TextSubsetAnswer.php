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
    protected ?array $answers;

    public function __construct(?array $answers = [])
    {
        $this->answers = $answers;
    }

    public function getAnswers() : ?array
    {
        return $this->answers;
    }
}
