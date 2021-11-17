<?php
declare(strict_types=1);

namespace srag\asq\Domain\Event;

use Fluxlabs\CQRS\Aggregate\AbstractValueObject;
use Fluxlabs\CQRS\Event\AbstractDomainEvent;
use ILIAS\Data\UUID\Uuid;
use DateTimeImmutable;

/**
 * Class QuestionMetadataSetEvent
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class QuestionMetadataSetEvent extends AbstractDomainEvent
{
    const META_KEY = 'meta';
    const META_FOR_KEY = 'meta_for';

    protected ?string $meta_for;

    protected ?AbstractValueObject $meta;

    public function __construct(
        Uuid $aggregate_id,
        DateTimeImmutable $occurred_on,
        ?AbstractValueObject $meta = null,
        ?string $meta_for = null
    ) {
        parent::__construct($aggregate_id, $occurred_on);

        $this->meta = $meta;
        $this->meta_for = $meta_for;
    }

    public function getMeta() : ?AbstractValueObject
    {
        return $this->meta;
    }

    public function getMetaFor() : ?string
    {
        return $this->meta_for;
    }

    public function getEventBody() : string
    {
        $body[self::META_FOR_KEY] = $this->meta_for;
        $body[self::META_KEY] = $this->meta;
        return json_encode($body);
    }

    public function restoreEventBody(string $json_data) : void
    {
        $body = json_decode($json_data, true);
        $this->meta_for = $body[self::META_FOR_KEY];
        $this->meta = AbstractValueObject::createFromArray($body[self::META_KEY]);
    }

    public static function getEventVersion() : int
    {
        // initial version 1
        return 1;
    }
}
