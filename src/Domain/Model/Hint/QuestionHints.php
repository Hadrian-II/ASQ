<?php
declare(strict_types=1);

namespace srag\asq\Domain\Model\Hint;

use srag\asq\Application\Exception\AsqException;
use Fluxlabs\CQRS\Aggregate\AbstractValueObject;

/**
 * Class Hints
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
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
