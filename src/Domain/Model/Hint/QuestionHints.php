<?php
declare(strict_types=1);

namespace srag\asq\Domain\Model\Hint;

use srag\asq\Application\Exception\AsqException;
use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class Hints
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionHints extends AbstractValueObject
{
    /**
     * @var ?QuestionHint[]
     */
    protected ?array $hints;

    /**
     * @param ?QuestionHint[] $hints
     */
    public function __construct(?array $hints = [])
    {
        $this->hints = $hints;
    }

    /**
     * @return ?QuestionHint[]
     */
    public function getHints() : ?array
    {
        return $this->hints;
    }

    public function getHintById(string $id) : QuestionHint
    {
        foreach ($this->hints as $hint) {
            if ($hint->getId() === $id) {
                return $hint;
            }
        }

        throw new AsqException(sprintf("Hint with Id: %s does not exist", $id));
    }
}
