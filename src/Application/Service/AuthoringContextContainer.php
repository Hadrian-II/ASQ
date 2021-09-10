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

    private ?IAuthoringCaller $caller;

    public function __construct(
        UiStandardLink $backLink,
        ?IAuthoringCaller $caller = null
    ) {
        $this->backLink = $backLink;
        $this->caller = $caller;
    }

    public function getBackLink() : UiStandardLink
    {
        return $this->backLink;
    }

    public function getCaller() : ?IAuthoringCaller
    {
        return $this->caller;
    }
}
