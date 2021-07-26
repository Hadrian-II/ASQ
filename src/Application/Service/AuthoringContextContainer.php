<?php
declare(strict_types=1);

namespace srag\asq\Application\Service;

use ILIAS\UI\Component\Link\Standard as UiStandardLink;

/**
 * Class AuthoringContextContainer
 *
 * Asq Authoring context stores information about the Calling object
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class AuthoringContextContainer
{
    private UiStandardLink $backLink;

    private int $refId;

    private int $objId;

    private string $objType;

    private int $actorId;

    private ?IAuthoringCaller $caller;

    public function __construct(
        UiStandardLink $backLink,
        int $refId,
        int $objId,
        string $objType,
        int $actorId,
        ?IAuthoringCaller $caller = null
    ) {
        $this->backLink = $backLink;
        $this->refId = $refId;
        $this->objId = $objId;
        $this->objType = $objType;
        $this->actorId = $actorId;
        $this->caller = $caller;
    }

    public function getBackLink() : UiStandardLink
    {
        return $this->backLink;
    }

    public function getRefId() : int
    {
        return $this->refId;
    }

    public function getObjId() : int
    {
        return $this->objId;
    }

    public function getObjType() : string
    {
        return $this->objType;
    }

    public function getActorId() : int
    {
        return $this->actorId;
    }

    public function getCaller() : ?IAuthoringCaller
    {
        return $this->caller;
    }
}
