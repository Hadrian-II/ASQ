<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Cloze\Editor\Data;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class ClozeGapConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
abstract class ClozeGapConfiguration extends AbstractValueObject
{
    const DEFAULT_FIELD_LENGTH = 80;

    const TYPE_TEXT = 'clz_text';

    const TYPE_NUMBER = 'clz_number';

    const TYPE_DROPDOWN = 'clz_dropdown';

    abstract public function getMaxPoints() : ?float;

    abstract public function isComplete() : bool;
}
