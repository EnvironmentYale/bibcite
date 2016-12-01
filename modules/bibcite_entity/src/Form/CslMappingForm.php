<?php

namespace Drupal\bibcite_entity\Form;


use Drupal\bibcite\Plugin\BibciteFormat;
use Drupal\Core\Form\FormStateInterface;

/**
 * Mapping form for CSL pseudo format.
 */
class CslMappingForm extends MappingForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $definition = [
      'id' => 'csl',
      'label' => $this->t('CSL'),
      'types' => [
        'bill',
        'book',
        'chapter',
        'broadcast',
        'paper-conference',
        'motion_picture',
        'article-journal',
        'legal_case',
        'article-magazine',
        'manuscript',
        'map',
        'article-newspaper',
        'patent',
        'personal_communication',
        'report',
        'legislation',
        'thesis',
        'webpage',
        'article',
        'dataset',
        'entry',
        'entry-dictionary',
        'entry-encyclopedia',
        'figure',
        'graphic',
        'interview',
        'musical_score',
        'pamphlet',
        'post',
        'post-weblog',
        'review',
        'review-book',
        'song',
        'speech',
        'treaty',
      ],
      'fields' => [
        'title',
        'type',
        'keyword',
        'author',
        'abstract',
        'issued',
        'collection-title',
        'container-title',
        'volume',
        'edition',
        'version',
        'chapter-number',
        'section',
        'issue',
        'number-of-volumes',
        'number',
        'page',
        'container',
        'event-date',
        'original-date',
        'publisher',
        'event-place',
        'publisher-place',
        'ISSN',
        'ISBN',
        'call-number',
        'citation-label',
        'URL',
        'DOI',
        'note',
        'original-title',
        'accessed',
        'annote',
        'archive',
        'archive_location',
        'archive-place',
        'author',
        'authority',
        'citation-number',
        'collection-editor',
        'collection-number',
        'composer',
        'container-author',
        'container-title',
        'container-title-short',
        'dimensions',
        'director',
        'editor',
        'editorial-director',
        'first-reference-note-number',
        'genre',
        'illustrator',
        'interviewer',
        'jurisdiction',
        'locator',
        'medium',
        'number-of-pages',
        'original-author',
        'original-publisher',
        'original-publisher-place',
        'page-first',
        'PMCID',
        'PMID',
        'recipient',
        'references',
        'reviewed-author',
        'reviewed-title',
        'scale',
        'source',
        'status',
        'submitted',
        'title-short',
        'translator',
        'year-suffix',
      ],
      'provider' => 'bibcite_entity',
    ];

    $bibcite_format = new BibciteFormat([], 'csl', $definition);

    return parent::buildForm($form, $form_state, $bibcite_format);
  }

}