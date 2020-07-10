<?php
declare(strict_types=1);

namespace srag\asq\Domain\Model;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class AbstractConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
abstract class AbstractConfiguration extends AbstractValueObject
{
    /**
     * Returns the Classname this Configuration contains configuration data for
     * Default assumes the configuration class is named ClassConfiguration, so it
     * returns Class
     *
     * @return string
     */
    public function configurationFor()
    {
        $class = get_called_class();
        return substr($class, 0, strlen($class) - strlen("Configuration"));
    }
}
