<?php
declare(strict_types=1);

namespace srag\asq\Application\Service;

use ILIAS\Data\Factory as DataFactory;
use ILIAS\Refinery\Factory;

/**
 * Class ASQDIC
 *
 * Temporary DIC for ASQ, to be integrated into ILIAS DIC on acceptance to core
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class ASQDIC extends \Pimple\Container
{
    const ASQ = 'ASQ';
    const QUESTION_SERVICE = 'QuestionService';
    const ANSWER_SERVICE = 'AnswerService';
    const UI_SERVICE = 'UIService';
    const LINK_SERVICE = 'LinkService';

    public static function initiateASQ(\Pimple\Container $c)
    {
        $object = new ASQDIC();

        $object[self::QUESTION_SERVICE] = function ($object) {
            return new QuestionService();
        };

        $object[self::ANSWER_SERVICE] = function ($object) {
            return new AnswerService();
        };

        $object[self::UI_SERVICE] = function ($object) {
            global $DIC;

            $data_factory = new DataFactory();
            $refinery = new Factory($data_factory, $DIC["lng"]);

            return new UIService(
                $DIC['lng'],
                $DIC->ui(),
                $DIC['http'],
                $data_factory,
                $refinery);
        };

        $object[self::LINK_SERVICE] = function ($object) {
            global $DIC;

            return new LinkService($DIC->ui(), $DIC['lng'], $DIC['ilCtrl']);
        };

        $object[self::ASQ] = function ($object) {
            return new AsqServices(
                $object[self::QUESTION_SERVICE],
                $object[self::ANSWER_SERVICE],
                $object[self::UI_SERVICE],
                $object[self::LINK_SERVICE]);
        };

        $GLOBALS['ASQDIC'] = $object;
    }

    public function asq() : AsqServices
    {
        return $this[self::ASQ];
    }
}