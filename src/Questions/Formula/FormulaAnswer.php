<?php
declare(strict_types=1);

namespace srag\asq\Questions\Formula;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class FormulaAnswer
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class FormulaAnswer extends AbstractValueObject
{
    protected ?array $variables;

    protected ?array $results;

    public function __construct(?array $variables = null, ?array $results = null)
    {
        $this->variables = $variables;
        $this->results = $results;
    }

    public function getVariables() : ?array
    {
        return $this->variables;
    }

    public function getResults() : ?array
    {
        return $this->results;
    }
}
