<?php
declare(strict_types=1);

namespace srag\asq\Questions\Cloze\Storage;

use DateTimeImmutable;
use Fluxlabs\CQRS\Event\DomainEvent;
use srag\asq\Domain\Event\QuestionAnswerOptionsSetEvent;
use srag\asq\Infrastructure\Persistence\RelationalEventStore\AbstractEventStorageHandler;

/**
 * Class ClozeAnswerOptionsSetEventHandler
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class ClozeAnswerOptionsSetEventHandler extends AbstractEventStorageHandler
{
    public function handleEvent(DomainEvent $event, int $event_id) : void
    {
        //nothing to do here
    }

    public function getQueryString(): string
    {
        return '';
    }

    public function createEvent(array $data, array $rows): DomainEvent
    {
        return new QuestionAnswerOptionsSetEvent(
            $this->factory->fromString($data['question_id']),
            (new DateTimeImmutable())->setTimestamp($data['occurred_on'])
        );
    }
}