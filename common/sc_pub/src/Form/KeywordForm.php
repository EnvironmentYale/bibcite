<?php

namespace Drupal\sc_pub\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Keyword edit forms.
 *
 * @ingroup sc_pub
 */
class KeywordForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\sc_pub\Entity\Keyword */
    $form = parent::buildForm($form, $form_state);
    $entity = $this->entity;

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;
    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Keyword.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Keyword.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.sc_pub_keyword.canonical', ['sc_pub_keyword' => $entity->id()]);
  }

}
