<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Component\Hint\Form;

use ILIAS\DI\UIServices;
use ilLanguage;
use ilRTE;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\Hint\QuestionHint;
use srag\asq\Domain\Model\Hint\QuestionHints;
use srag\asq\UserInterface\Web\PathHelper;
use srag\asq\UserInterface\Web\Fields\AsqTableInput;
use srag\asq\UserInterface\Web\Fields\AsqTableInputFieldDefinition;

/**
 * Class HintFormGUI
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Martin Studer <ms@studer-raimann.ch>
 */
class HintFormGUI extends \ilPropertyFormGUI
{
    use PathHelper;

    const HINT_POSTVAR = 'hints';
    const HINT_CONTENT_POSTVAR = 'hint_content';
    const HINT_POINTS_POSTVAR = 'hint_points';

    /**
     * @var QuestionDto
     */
    private $question_dto;

    /**
     * @var ilLanguage
     */
    private $language;

    /**
     * @var UIServices
     */
    private $ui;

    /**
     * @var AsqTableInput
     */
    private $hint_table;

    /**
     * @param QuestionDto $question_dto
     * @param UIServices $ui
     * @param ilLanguage $language
     */
    public function __construct(QuestionDto $question_dto, UIServices $ui, ilLanguage $language)
    {
        parent::__construct();

        $this->question_dto = $question_dto;
        $this->ui = $ui;
        $this->language = $language;

        $this->setTitle(sprintf($this->language->txt('asq_question_hints_form_header'), $this->question_dto->getData()->getTitle()));

        $this->hint_table = new AsqTableInput(
            $this->language->txt('asq_hints'),
            self::HINT_POSTVAR,
            $this->getHintData(),
            $this->getTableDefinitions(),
            [AsqTableInput::OPTION_ORDER]
        );

        $this->addItem($this->hint_table);

        $rtestring = ilRTE::_getRTEClassname();
        include_once "./Services/RTE/classes/class.$rtestring.php";
        $rte = new $rtestring();
        $rte->addRTESupport(55, 'blah');

        $this->ui->mainTemplate()->addJavaScript($this->getBasePath(__DIR__) . 'js/AssessmentQuestionAuthoring.js');
    }

    /**
     * @return array
     */
    private function getHintData() : array
    {
        if (!$this->question_dto->hasHints()) {
            return [];
        }

        return array_map(function ($hint) {
            return [
                self::HINT_CONTENT_POSTVAR => $hint->getContent(),
                self::HINT_POINTS_POSTVAR => $hint->getPointDeduction()];
        }, $this->question_dto->getQuestionHints()->getHints());
    }

    /**
     * @return AsqTableInputFieldDefinition[]
     */
    private function getTableDefinitions() : array
    {
        return [
            new AsqTableInputFieldDefinition(
                $this->language->txt('asq_question_hints_label_hint'),
                AsqTableInputFieldDefinition::TYPE_TEXT_AREA,
                self::HINT_CONTENT_POSTVAR
            ),
            new AsqTableInputFieldDefinition(
                $this->language->txt('asq_question_hints_label_points_deduction'),
                AsqTableInputFieldDefinition::TYPE_NUMBER,
                self::HINT_POINTS_POSTVAR
            )
        ];
    }

    /**
     * @return QuestionHints
     */
    public function getHintsFromPost() : QuestionHints
    {
        $index = 0;

        return QuestionHints::create(
            array_map(
                function ($raw_hint) use ($index) {
                    $index += 1;

                    return QuestionHint::create(
                        strval($index),
                        $raw_hint[self::HINT_CONTENT_POSTVAR],
                        floatval($raw_hint[self::HINT_POINTS_POSTVAR])
                    );
                },
                $this->hint_table->readValues()
            )
        );
    }
}
