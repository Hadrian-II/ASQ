<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Ordering\Form;

use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\Answer\Option\AnswerOption;
use srag\asq\Domain\Model\Answer\Option\AnswerOptions;
use srag\asq\Domain\Model\Answer\Option\EmptyDefinition;
use srag\asq\Domain\Model\Answer\Option\EmptyDefinitionFactory;
use srag\asq\Domain\Model\Answer\Option\ImageAndTextDisplayDefinition;
use srag\asq\UserInterface\Web\Form\QuestionFormFactory;
use ilLanguage;

/**
 * Class OrderingFormFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class OrderingTextFormFactory extends QuestionFormFactory
{
    public function __construct(ilLanguage $language)
    {
        parent::__construct(
            new OrderingTextEditorConfigurationFactory($language),
            new OrderingScoringConfigurationFactory($language),
            new EmptyDefinitionFactory($language),
            new EmptyDefinitionFactory($language)
        );
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Form\QuestionFormFactory::performQuestionPostProcessing()
     */
    public function performQuestionPostProcessing(QuestionDto $question) : QuestionDto
    {
        $text_input = $question->getPlayConfiguration()->getEditorConfiguration()->getText();

        $options = [];

        $i = 1;
        if (!empty($text_input)) {
            $words = explode(' ', $text_input);

            foreach ($words as $word) {
                $options[] = AnswerOption::create(
                    strval($i),
                    ImageAndTextDisplayDefinition::create($word, ''),
                    EmptyDefinition::create()
                );

                $i += 1;
            }
        }

        $question->setAnswerOptions(AnswerOptions::create($options));

        return $question;
    }
}
