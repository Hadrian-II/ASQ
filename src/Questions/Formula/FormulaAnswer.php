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
    /**
     * @var ?array
     */
    protected $variables;

    /**
     * @var ?array
     */
    protected $results;

    /**
     * @param array $values
     */
    public function __construct(?array $variables = null, ?array $results = null)
    {
        $this->variables = $variables;
        $this->results = $results;
    }

    /**
     * @return ?array
     */
    public function getVariables() : ?array
    {
        return $this->variables;
    }

    /**
     * @return ?array
     */
    public function getResults() : ?array
    {
        return $this->results;
    }
}
