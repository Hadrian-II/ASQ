<?php
declare(strict_types=1);

namespace srag\asq\Application\Service;

/**
 * Class ASQService
 *
 * Base für Asq Services to allow user impersonation
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
abstract class ASQService
{
    private ?int $user_id;

    protected function getActiveUser() : int
    {
        global $DIC;

        return $this->user_id ?? intval($DIC->user()->getId());
    }

    public function setActiveUser(int $id) : void
    {
        $this->user_id = $id;
    }
}
