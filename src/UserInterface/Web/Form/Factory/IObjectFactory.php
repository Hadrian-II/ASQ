<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Form\Factory;

use Fluxlabs\CQRS\Aggregate\AbstractValueObject;

/**
 * Interface ObjectFactory
 *
 * Defines the methods a FormFactory has to implement
 * Methods contain generation of form fields and creating
 * Value objects from POST
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
interface IObjectFactory
{
    /**
     * Gets field definition to display in QuestionFormGUI
     *
     * @param ?AbstractValueObject $value
     * @return array
     */
    public function getFormfields(?AbstractValueObject $value) : array;

    /**
     * Reads configuration object from postdata
     *
     * @param array $postdata
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
