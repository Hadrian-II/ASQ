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
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class AuthoringContextContainer
{
    /**
     * @var UiStandardLink
     */
    private $backLink;

    /**
     * @var int
     */
    private $refId;

    /**
     * @var int
     */
    private $objId;

    /**
     * @var string
     */
    private $objType;

    /**
     * @var int
     */
    private $actorId;

    /**
     * @var ?IAuthoringCaller
     */
    private $caller;

    /**
     * AuthoringContextContainer constructor.
     *
     * @param UiStandardLink $backLink
     * @param int            $refId
     * @param int            $objId
     * @param string         $objType
     * @param int            $actorId
     * @param bool           $writeAccess
     */
    public function __construct(
        UiStandardLink $backLink,
        int $refId,
        int $objId,
        string $objType,
        int $actorId,
        ?IAuthoringCaller $caller = null
    )
    {
        $this->backLink = $backLink;
        $this->refId = $refId;
        $this->objId = $objId;
        $this->objType = $objType;
        $this->actorId = $actorId;
        $this->caller = $caller;
    }


    /**
     * @return UiStandardLink
     */
    public function getBackLink() : UiStandardLink
    {
        return $this->backLink;
    }


    /**
     * @return int
     */
    public function getRefId() : int
    {
        return $this->refId;
    }


    /**
     * @return int
     */
    public function getObjId() : int
    {
        return $this->objId;
    }


    /**
     * @return string
     */
    public function getObjType() : string
    {
        return $this->objType;
    }


    /**
     * @return int
     */
    public function getActorId() : int
    {
        return $this->actorId;
    }

    /**
     * @return ?IAuthoringCaller
     */
    public function getCaller() : ?IAuthoringCaller
    {
        return $this->caller;
    }
}
