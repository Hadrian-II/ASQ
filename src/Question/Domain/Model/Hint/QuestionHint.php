<?php
declare(strict_types=1);

namespace srag\asq\Domain\Model\Hint;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Application\Exception\AsqException;

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
     * @var string
     */
    protected $id;
    /**
     * @var string
     */
    protected $content;
    /**
     * @var float
     */
    protected $point_deduction;

    /**
     * Hint constructor.
     *
     * @param string $id
     * @param string $content
     * @param float  $point_deduction
     *
     * @throws AsqException
     */
    public static function create(string $id, string $content, float $point_deduction) : QuestionHint
    {
        $object = new QuestionHint();
        $object->id = $id;
        $object->content = $content;
        $object->point_deduction = $point_deduction;
        return $object;
    }

    /**
     * @return string
     */
    public function getId() : string
    {
        return $this->id;
    }


    /**
     * @return string
     */
    public function getContent() : string
    {
        return $this->content;
    }


    /**
     * @return float
     */
    public function getPointDeduction() : float
    {
        return $this->point_deduction;
    }
}
