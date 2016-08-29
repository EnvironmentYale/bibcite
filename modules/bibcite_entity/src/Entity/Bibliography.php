<?php

namespace Drupal\bibcite_entity\Entity;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;
use Drupal\bibcite\CslKeyConverter;

/**
 * Defines the Bibliography entity.
 *
 * @ingroup bibcite_entity
 *
 * @ContentEntityType(
 *   id = "bibliography",
 *   label = @Translation("Bibliography"),
 *   handlers = {
 *     "view_builder" = "Drupal\bibcite_entity\BibliographyViewBuilder",
 *     "list_builder" = "Drupal\bibcite_entity\BibliographyListBuilder",
 *     "views_data" = "Drupal\bibcite_entity\BibliographyViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\bibcite_entity\Form\BibliographyForm",
 *       "delete" = "Drupal\bibcite_entity\Form\BibliographyDeleteForm",
 *     },
 *     "access" = "Drupal\bibcite_entity\BibliographyAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\bibcite_entity\BibliographyHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "bibliography",
 *   admin_permission = "administer bibliography entities",
 *   fieldable = FALSE,
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "title",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode",
 *   },
 *   links = {
 *     "canonical" = "/bibcite/bibliography/{bibliography}",
 *     "edit-form" = "/bibcite/bibliography/{bibliography}/edit",
 *     "delete-form" = "/bibcite/bibliography/{bibliography}/delete",
 *     "add-form" = "/admin/content/bibliography/add",
 *     "collection" = "/admin/content/bibliography",
 *   },
 * )
 */
class Bibliography extends ContentEntityBase implements BibliographyInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public function cite($style = NULL) {
    // @todo Make a better dependency injection.
    $styler = \Drupal::service('bibcite.styler');
    $serializer = \Drupal::service('serializer');

    $data = $serializer->normalize($this, 'csl');
    return $styler->render($data, $style);
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    /*
     * Main attributes.
     */

    $fields['type'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Type'))
      ->setDescription(t('The type of publication for the Bibliography'))
      ->setSettings([
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 1,
      ])
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => 1,
      ]);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setDescription(t('The title of the Bibliography.'))
      ->setSettings([
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 2,
      ]);

    $fields['author'] = BaseFieldDefinition::create('bibcite_contributor')
      ->setLabel(t('Author'))
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setDisplayOptions('form', [
        'type' => 'bibcite_contributor_widget',
        'weight' => 3,
      ])
      ->setDisplayOptions('view', [
        'type' => 'bibcite_contributor_label',
        'weight' => 3,
      ]);

    $fields['keywords'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Keywords'))
      ->setSetting('target_type', 'bibcite_keyword')
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete_tags',
        'weight' => 4,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'placeholder' => '',
        ],
      ])
      ->setDisplayOptions('view', [
        'type' => 'entity_reference_label',
        'weight' => 4,
      ]);

    /*
     * CSL fields.
     */

    $weight = 5;

    $default_string = function($label) use (&$weight) {
      $weight++;
      return BaseFieldDefinition::create('string')
        ->setLabel($label)
        ->setDefaultValue('')
        ->setDisplayOptions('view', [
          'label' => 'above',
          'type' => 'string',
          'weight' => $weight,
        ])
        ->setDisplayOptions('form', [
          'type' => 'string_textfield',
          'weight' => $weight,
        ]);
    };

    $default_integer = function ($label) use (&$weight) {
      $weight++;
      return BaseFieldDefinition::create('integer')
        ->setLabel($label)
        ->setDefaultValue(NULL)
        ->setDisplayOptions('form', [
          'type' => 'number',
          'weight' => $weight,
        ])
        ->setDisplayOptions('view', [
          'type' => 'number_integer',
          'weight' => $weight,
        ]);
    };

    $default_string_long = function ($label, $rows = 1) use (&$weight) {
      $weight++;
      return BaseFieldDefinition::create('string_long')
        ->setLabel($label)
        ->setDisplayOptions('view', [
          'type' => 'text_default',
          'weight' => $weight,
        ])
        ->setDisplayOptions('form', [
          'type' => 'string_textarea',
          'settings' => [
            'rows' => $rows,
          ],
          'weight' => $weight,
        ]);
    };

    /*
     * Text fields.
     */
    $fields['bibcite_abst_e'] = $default_string_long(t('Abstract'), 4);
    $fields['bibcite_abst_f'] = $default_string_long(t('French Abstract'), 4);
    $fields['bibcite_notes'] = $default_string_long(t('Notes'), 4);
    $fields['bibcite_custom1'] = $default_string_long(t('Custom 1'));
    $fields['bibcite_custom2'] = $default_string_long(t('Custom 2'));
    $fields['bibcite_custom3'] = $default_string_long(t('Custom 3'));
    $fields['bibcite_custom4'] = $default_string_long(t('Custom 4'));
    $fields['bibcite_custom5'] = $default_string_long(t('Custom 5'));
    $fields['bibcite_custom6'] = $default_string_long(t('Custom 6'));
    $fields['bibcite_custom7'] = $default_string_long(t('Custom 7'));
    $fields['bibcite_auth_address'] = $default_string_long(t('Author Address'));

    /*
     * Number fields.
     */
    $fields['bibcite_year'] = $default_integer(t('Year of Publication'));

    /*
     * String fields.
     */
    $fields['bibcite_secondary_title'] = $default_string(t('Secondary Title'));
    $fields['bibcite_volume'] = $default_string(t('Volume'));
    $fields['bibcite_edition'] = $default_string(t('Edition'));
    $fields['bibcite_section'] = $default_string(t('Section'));
    $fields['bibcite_issue'] = $default_string(t('Issue'));
    $fields['bibcite_number_of_volumes'] = $default_string(t('Number of Volumes'));
    $fields['bibcite_number'] = $default_string(t('Number'));
    $fields['bibcite_pages'] = $default_string(t('Pagination'));
    $fields['bibcite_date'] = $default_string(t('Date Published'));
    $fields['bibcite_type_of_work'] = $default_string(t('Type of Work'));
    $fields['bibcite_lang'] = $default_string(t('Publication Language'));
    $fields['bibcite_reprint_edition'] = $default_string(t('Reprint Edition'));
    $fields['bibcite_publisher'] = $default_string(t('Publisher'));
    $fields['bibcite_place_published'] = $default_string(t('Place Published'));
    $fields['bibcite_issn'] = $default_string(t('ISSN Number'));
    $fields['bibcite_isbn'] = $default_string(t('ISBN Number'));
    $fields['bibcite_accession_number'] = $default_string(t('Accession Number'));
    $fields['bibcite_call_number'] = $default_string(t('Call Number'));
    $fields['bibcite_other_number'] = $default_string(t('Other Numbers'));
    $fields['bibcite_citekey'] = $default_string(t('Citation Key'));
    $fields['bibcite_url'] = $default_string(t('URL'));
    $fields['bibcite_doi'] = $default_string(t('DOI'));
    $fields['bibcite_research_notes'] = $default_string(t('Reseach Notes'));
    $fields['bibcite_tertiary_title'] = $default_string(t('Tertiary Title'));
    $fields['bibcite_short_title'] = $default_string(t('Short Title'));
    $fields['bibcite_alternate_title'] = $default_string(t('Alternate Title'));
    $fields['bibcite_translated_title'] = $default_string(t('Translated Title'));
    $fields['bibcite_original_publication'] = $default_string(t('Original Publication'));
    $fields['bibcite_other_author_affiliations'] = $default_string(t('Other Author Affiliations'));
    $fields['bibcite_remote_db_name'] = $default_string(t('Remote Database Name'));
    $fields['bibcite_remote_db_provider'] = $default_string(t('Remote Database Provider'));
    $fields['bibcite_label'] = $default_string(t('Label'));
    $fields['bibcite_access_date'] = $default_string(t('Access Date'));
    $fields['bibcite_refereed'] = $default_string(t('Refereed Designation'));

    $fields['bibcite_pmid'] = $default_string(t('PMID'));

    /*
     * Entity dates.
     */

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
