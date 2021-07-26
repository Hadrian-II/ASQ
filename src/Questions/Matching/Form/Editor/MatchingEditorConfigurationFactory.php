<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Matching\Form\Editor;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Questions\Matching\Editor\Data\MatchingEditorConfiguration;
use srag\asq\Questions\Matching\Editor\Data\MatchingItem;
use srag\asq\Questions\Matching\Editor\Data\MatchingMapping;
use srag\asq\UserInterface\Web\Fields\AsqTableInput\AsqTableInput;
use srag\asq\UserInterface\Web\Fields\AsqTableInput\AsqTableInputFieldDefinition;
use srag\asq\UserInterface\Web\Form\Factory\AbstractObjectFactory;

/**
 * Class MatchingEditorConfigurationFactory
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class MatchingEditorConfigurationFactory extends AbstractObjectFactory
{
    const VAR_SHUFFLE = 'me_shuffle';
    const VAR_THUMBNAIL = 'me_thumbnail';
    const VAR_MATCHING_MODE = 'me_matching';

    const VAR_DEFINITIONS = 'me_definitions';
    const VAR_DEFINITION_TEXT = 'me_definition_text';
    const VAR_DEFINITION_IMAGE = 'me_definition_image';

    const VAR_TERMS = 'me_terms';
    const VAR_TERM_TEXT = 'me_term_text';
    const VAR_TERM_IMAGE = 'me_term_image';

    const VAR_MATCHES = 'me_matches';
    const VAR_MATCH_DEFINITION = 'me_match_definition';
    const VAR_MATCH_TERM = 'me_match_term';
    const VAR_MATCH_POINTS = 'me_match_points';

    public function getFormfields(?AbstractValueObject $value) : array
    {
        $fields = [];
        /** @var MatchingEditorConfiguration $config */

        $shuffle_answers = $this->factory->input()->field()->select(
            $this->language->txt('asq_label_shuffle_answers'),
            [
                MatchingEditorConfiguration::SHUFFLE_NONE
                    => $this->language->txt('asq_option_shuffle_none'),
                MatchingEditorConfiguration::SHUFFLE_DEFINITIONS
                    => $this->language->txt('asq_option_shuffle_definitions'),
                MatchingEditorConfiguration::SHUFFLE_TERMS
                    => $this->language->txt('asq_option_shuffle_terms'),
                MatchingEditorConfiguration::SHUFFLE_BOTH
                    => $this->language->txt('asq_option_shuffle_both')
            ]
        );

        $thumbnail = $this->factory->input()->field()->numeric($this->language->txt('asq_label_thumbnail'));

        $matching_mode = $this->factory->input()->field()->radio($this->language->txt('asq_label_matching_mode'))
        ->withOption(
            strval(MatchingEditorConfiguration::MATCHING_ONE_TO_ONE),
            $this->language->txt('asq_option_one_to_one')
        )
        ->withOption(
            strval(MatchingEditorConfiguration::MATCHING_MANY_TO_ONE),
            $this->language->txt('asq_option_many_to_one')
        )
        ->withOption(
            strval(MatchingEditorConfiguration::MATCHING_MANY_TO_MANY),
            $this->language->txt('asq_option_many_to_many')
        )
        ->withAdditionalOnLoadCode(function($id) {
            return "il.ASQ.Matching.setModeSelect($($id));";
        });


        if (!is_null($value)) {
            $shuffle_answers = $shuffle_answers->withValue($value->getShuffle());
            $thumbnail = $thumbnail->withValue($value->getThumbnailSize());
            $matching_mode = $matching_mode->withValue(
                strval($value->getMatchingMode() ?? MatchingEditorConfiguration::MATCHING_ONE_TO_ONE)
            );
        }

        $fields[self::VAR_SHUFFLE] = $shuffle_answers;
        $fields[self::VAR_THUMBNAIL] = $thumbnail;
        $fields[self::VAR_MATCHING_MODE] = $matching_mode;

        $fields[self::VAR_DEFINITIONS] = $this->createDefinitionsTable($value);

        $fields[self::VAR_TERMS] = $this->createTermsTable($value);

        $fields[self::VAR_MATCHES] = $this->createMatchTable($value);

        return $fields;
    }

    private function createDefinitionsTable(?MatchingEditorConfiguration $config) : AsqTableInput
    {
        $columns = [];

        $columns[] = new AsqTableInputFieldDefinition(
            $this->language->txt('asq_header_definition_text'),
            AsqTableInputFieldDefinition::TYPE_TEXT,
            self::VAR_DEFINITION_TEXT
        );

        $columns[] = new AsqTableInputFieldDefinition(
            $this->language->txt('asq_header_definition_image'),
            AsqTableInputFieldDefinition::TYPE_IMAGE,
            self::VAR_DEFINITION_IMAGE
        );

        $table = $this->asq_ui->getAsqTableInput(
            $this->language->txt('asq_label_definitions'),
            $columns
        )
        ->withAdditionalOnLoadCode(function($id) {
            return "il.ASQ.Matching.setDefinitionsTable($($id));";
        });

        if (!is_null($config)) {
            $table = $table->withValue(
                $this->getItemValues(
                    $config->getDefinitions(),
                    self::VAR_DEFINITION_TEXT,
                    self::VAR_DEFINITION_IMAGE
                )
            );
        }

        return $table;
    }

    private function createTermsTable(?MatchingEditorConfiguration $config) : AsqTableInput
    {
        $columns = [];

        $columns[] = new AsqTableInputFieldDefinition(
            $this->language->txt('asq_header_term_text'),
            AsqTableInputFieldDefinition::TYPE_TEXT,
            self::VAR_TERM_TEXT
        );

        $columns[] = new AsqTableInputFieldDefinition(
            $this->language->txt('asq_header_term_image'),
            AsqTableInputFieldDefinition::TYPE_IMAGE,
            self::VAR_TERM_IMAGE
        );

        $table = $this->asq_ui->getAsqTableInput(
            $this->language->txt('asq_label_terms'),
            $columns
        )
        ->withAdditionalOnLoadCode(function($id) {
            return "il.ASQ.Matching.setTermsTable($($id));";
        });

        if (!is_null($config)) {
            $table = $table->withValue(
                $this->getItemValues(
                    $config->getTerms(),
                    self::VAR_TERM_TEXT,
                    self::VAR_TERM_IMAGE
                )
            );
        }

        return $table;
    }

    private function getItemValues(array $items, string $text_name, string $image_name) : array
    {
        return array_map(
            function ($item) use ($text_name, $image_name) {
                return [
                    $text_name => $item->getText(),
                    $image_name => $item->getImage()
                ];
            },
            $items
        );
    }

    private function createMatchTable(?MatchingEditorConfiguration $config) : AsqTableInput
    {
        $columns = [];

        $defs = [];

        foreach ($config->getDefinitions() as $definition) {
            $defs[$definition->getId()] = $definition->getText();
        }

        $columns[] = new AsqTableInputFieldDefinition(
            $this->language->txt('asq_header_matches_definition'),
            AsqTableInputFieldDefinition::TYPE_DROPDOWN,
            self::VAR_MATCH_DEFINITION,
            $defs
        );

        $terms = [];

        foreach ($config->getTerms() as $term) {
            $terms[$term->getId()] = $term->getText();
        }

        $columns[] = new AsqTableInputFieldDefinition(
            $this->language->txt('asq_header_matches_term'),
            AsqTableInputFieldDefinition::TYPE_DROPDOWN,
            self::VAR_MATCH_TERM,
            $terms
        );

        $columns[] = new AsqTableInputFieldDefinition(
            $this->language->txt('asq_header_points'),
            AsqTableInputFieldDefinition::TYPE_NUMBER,
            self::VAR_MATCH_POINTS
        );

        $table = $this->asq_ui->getAsqTableInput(
            $this->language->txt('asq_label_matches'),
            $columns
            )
            ->withAdditionalOnLoadCode(function($id) {
                return "il.ASQ.Matching.setMatchTable($($id));";
            });

        if (!is_null($config)) {
            $table = $table->withValue($this->getMatchesValues($config));
        }

        return $table;
    }

    private function getMatchesValues(MatchingEditorConfiguration $config) : array
    {
        return array_map(
            function ($match) {
                return [
                    self::VAR_MATCH_DEFINITION => $match->getDefinitionId(),
                    self::VAR_MATCH_TERM => $match->getTermId(),
                    self::VAR_MATCH_POINTS => $match->getPoints()
                ];
            },
            $config->getMatches()
        );
    }

    /**
     * @return MatchingEditorConfiguration
     */
    public function readObjectFromPost(array $postdata) : AbstractValueObject
    {
        $id = -1;

        $def = array_map(
            function ($value) use (&$id) {
                $id += 1;

                return new MatchingItem(
                    strval($id),
                    $value[self::VAR_DEFINITION_TEXT],
                    $value[self::VAR_DEFINITION_IMAGE]
                );
            },
            $postdata[self::VAR_DEFINITIONS]
        );

        $id = -1;

        $term = array_map(
            function ($value) use (&$id) {
                $id += 1;

                return new MatchingItem(
                    strval($id),
                    $value[self::VAR_TERM_TEXT],
                    $value[self::VAR_TERM_IMAGE]
                );
            },
            $postdata[self::VAR_TERMS]
        );

        $match = array_map(
            function ($value) {
                return new MatchingMapping(
                    $value[self::VAR_MATCH_DEFINITION],
                    $value[self::VAR_MATCH_TERM],
                    $this->readFloat($value[self::VAR_MATCH_POINTS])
                );
            },
            $postdata[self::VAR_MATCHES]
        );

        return new MatchingEditorConfiguration(
            $this->readInt($postdata[self::VAR_SHUFFLE]),
            $postdata[self::VAR_THUMBNAIL],
            $this->readInt($postdata[self::VAR_MATCHING_MODE]),
            $def,
            $term,
            $match
        );
    }

    /**
     * @return MatchingEditorConfiguration
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return new MatchingEditorConfiguration();
    }
}
