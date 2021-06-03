<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Component\Hint\Form;

use ILIAS\DI\UIServices;
use ILIAS\UI\Component\Input\Container\Form\Standard;
use Psr\Http\Message\RequestInterface;
use ilLanguage;
use srag\asq\Application\Service\UIService;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\Hint\QuestionHint;
use srag\asq\Domain\Model\Hint\QuestionHints;
use srag\asq\Infrastructure\Helpers\PathHelper;
use srag\asq\UserInterface\Web\Fields\AsqTableInput\AsqTableInput;
use srag\asq\UserInterface\Web\Fields\AsqTableInput\AsqTableInputFieldDefinition;
use srag\asq\UserInterface\Web\Form\InputHandlingTrait;

/**
 * Class HintFormGUI
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Martin Studer <ms@studer-raimann.ch>
 */
class HintFormGUI
{
    use PathHelper;
    use InputHandlingTrait;

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
     * @var RequestInterface
     */
    private $request;

    /**
     * @var Standard
     */
    private $form;

    /**
     * @var UIService
     */
    private $asq_ui;

    /**
     * @param QuestionDto $question_dto
     * @param string $action
     * @param ilLanguage $language
     * @param UIServices $ui
     * @param RequestInterface $request
     */
    public function __construct(
        QuestionDto $question_dto,
        string $action,
        ilLanguage $language,
        UIServices $ui,
        RequestInterface $request,
        UIService $asq_ui)
    {
        $this->question_dto = $question_dto;
        $this->ui = $ui;
        $this->language = $language;
        $this->request = $request;
        $this->asq_ui = $asq_ui;

        $this->form = $this->ui->factory()->input()->container()->form()->standard($action, [
            self::HINT_POSTVAR =>
                $this->asq_ui->getAsqTableInput(
                    $this->language->txt('asq_hints'),
                    [
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
                    ]
                )
                ->withOptions([AsqTableInput::OPTION_ORDER => true])
                ->withValue($this->getHintData())
        ]);
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
     * @return string
     */
    public function getHTML() : string
    {
        $panel = $this->ui->factory()->panel()->standard(
            sprintf(
                $this->language->txt('asq_question_hints_form_header'),
                $this->language->txt($this->question_dto->getType()->getTitleKey())),
            $this->form
        );

        return $this->ui->renderer()->render($panel);
    }

    /**
     * @return QuestionHints
     */
    public function getHintsFromPost() : QuestionHints
    {
        $this->form = $this->form->withRequest($this->request);
        $postdata = $this->form->getData();

        $index = 0;

        return new QuestionHints(
            array_map(
                function ($raw_hint) use ($index) {
                    $index += 1;

                    return new QuestionHint(
                        strval($index),
                        $raw_hint[self::HINT_CONTENT_POSTVAR],
                        $this->readFloat($raw_hint[self::HINT_POINTS_POSTVAR])
                    );
                },
                $postdata[self::HINT_POSTVAR]
            )
        );
    }
}
