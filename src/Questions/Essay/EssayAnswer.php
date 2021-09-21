<?php
declare(strict_types=1);

namespace srag\asq\Questions\Essay;

use Fluxlabs\CQRS\Aggregate\AbstractValueObject;

/**
 * Class EssayAnswer
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class EssayAnswer extends AbstractValueObject
{
    protected ?string $text;

    public function __construct(?string $text = null)
    {
        $this->text = $text;
    }

    public function getText() : ?string
    {
        return $this->text;
    }
}
