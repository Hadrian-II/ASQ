<?php
declare(strict_types=1);

namespace srag\asq\Application\Service;

/**
 * Class ASQService
 *
 * Base für Asq Services to allow user impersonation
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
abstract class ASQService {
    /**
     * @var ?int
     */
    private $user_id;

    protected function getActiveUser() : int{
        global $DIC;

        return $this->user_id ?? intval($DIC->user()->getId());
    }

    public function setActiveUser(int $id) {
        $this->user_id = $id;
    }
}