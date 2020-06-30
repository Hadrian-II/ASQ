<?php
declare(strict_types=1);

namespace srag\asq\Domain\Model;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Infrastructure\Persistence\QuestionType;
use srag\asq\UserInterface\Web\Form\QuestionFormGUI;

/**
 * Class QuestionTypeDefinition
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionTypeDefinition extends AbstractValueObject
{
    /**
     * @var string
     */
    protected $title_key;

    /**
     * @var string
     */
    protected $factory_class;

    /**
     * @param QuestionType $type
     * @return QuestionTypeDefinition
     */
    public static function create(QuestionType $type) : QuestionTypeDefinition
    {
        $object = new QuestionTypeDefinition();
        $object->title_key = $type->getTitleKey();
        $object->factory_class = $type->getFactoryClass();
        return $object;
    }

    /**
     * @return string
     */
    public function getTitleKey() : string
    {
        return $this->title_key;
    }

    /**
     * @return QuestionFormGUI
     */
    public function getFactoryClass() : string
    {
        return $this->factory_class;
    }
}
