<?php
declare(strict_types=1);

namespace srag\asq\Domain\Model\Hint;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class QuestionHint
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionHint extends AbstractValueObject
{
    protected ?string $id;

    protected ?string $content;

    protected ?float $point_deduction;

    public function __construct(?string $id = null, ?string $content = null, ?float $point_deduction = null)
    {
        $this->id = $id;
        $this->content = $content;
        $this->point_deduction = $point_deduction;
    }

    public function getId() : ?string
    {
        return $this->id;
    }

    public function getContent() : ?string
    {
        return $this->content;
    }

    public function getPointDeduction() : ?float
    {
        return $this->point_deduction;
    }
}
