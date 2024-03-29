<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Ordering\Form;

use ILIAS\DI\UIServices;
use ilLanguage;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\Answer\Option\AnswerOption;
use srag\asq\Questions\Generic\Data\EmptyDefinition;
use srag\asq\Questions\Generic\Data\ImageAndTextDisplayDefinition;
use srag\asq\Questions\Generic\Form\EmptyDefinitionFactory;
use srag\asq\Questions\Ordering\Form\Editor\OrderingTextEditorConfigurationFactory;
use srag\asq\Questions\Ordering\Form\Scoring\OrderingScoringConfigurationFactory;
use srag\asq\UserInterface\Web\Form\Factory\QuestionFormFactory;
use srag\asq\Application\Service\UIService;

/**
 * Class OrderingFormFactory
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class OrderingTextFormFactory extends QuestionFormFactory
{
    public function __construct(ilLanguage $language, UIServices $ui, UIService $asq_ui)
    {
        parent::__construct(
            new OrderingTextEditorConfigurationFactory($language, $ui, $asq_ui),
            new OrderingScoringConfigurationFactory($language, $ui, $asq_ui),
            new EmptyDefinitionFactory($language, $ui),
            new EmptyDefinitionFactory($language, $ui)
        );
    }

    public function performQuestionPostProcessing(QuestionDto $question) : QuestionDto
    {
        $text_input = $question->getPlayConfiguration()->getEditorConfiguration()->getText();

        $options = [];

        $i = 1;
        if (!empty($text_input)) {
            $words = explode(' ', $text_input);

            foreach ($words as $word) {
                $options[] = new AnswerOption(
                    strval($i),
                    new ImageAndTextDisplayDefinition($word, ''),
                    new EmptyDefinition()
                );

                $i += 1;
            }
        }

        $question->setAnswerOptions($options);

        return $question;
    }
}
