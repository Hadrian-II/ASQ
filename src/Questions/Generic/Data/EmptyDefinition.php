<?php
declare(strict_types=1);

namespace srag\asq\Questions\Generic\Data;

use Fluxlabs\CQRS\Aggregate\AbstractValueObject;

/**
 * Class EmptyDefinition
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class EmptyDefinition extends AbstractValueObject
{
    public function __construct() {}
}
