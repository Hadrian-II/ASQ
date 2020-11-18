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
    /**
     * @var ?string
     */
    protected $id;
    /**
     * @var ?string
     */
    protected $content;
    /**
     * @var ?float
     */
    protected $point_deduction;

    /**
     * @param ?string $id
     * @param ?string $content
     * @param ?float $point_deduction
     */
    public function __construct(?string $id = null, ?string $content = null, ?float $point_deduction = null)
    {
        $this->id = $id;
        $this->content = $content;
        $this->point_deduction = $point_deduction;
    }

    /**
     * @return ?string
     */
    public function getId() : ?string
    {
        return $this->id;
    }


    /**
     * @return ?string
     */
    public function getContent() : ?string
    {
        return $this->content;
    }


    /**
     * @return ?float
     */
    public function getPointDeduction() : ?float
    {
        return $this->point_deduction;
    }
}
