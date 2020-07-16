<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Form\Factory;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Interface ObjectFactory
 *
 * Defines the methods a FormFactory has to implement
 * Methods contain generation of form fields and creating
 * Value objects from POST
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
interface IObjectFactory
{
    /**
     * Gets field definition to display in QuestionFormGUI
     *
     * @return array
     */
    public function getFormfields(?AbstractValueObject $value) : array;

    /**
     * Reads configuration object from postdata
     *
     * @return AbstractValueObject
     */
    public function readObjectFromPost(array $postdata) : AbstractValueObject;

    /**
     * Creates new object containing the default values
     *
     * @return AbstractValueObject
     */
    public function getDefaultValue() : AbstractValueObject;
}
