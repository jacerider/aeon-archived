<?php

namespace Drupal\aeon\Plugin\Form;

use Drupal\aeon\Utility\Element;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hook_form_FORM_ID_alter().
 *
 * @ingroup plugins_form
 *
 * @AeonForm("search_form")
 */
class SearchForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function alterFormElement(Element $form, FormStateInterface $form_state, $form_id = NULL) {
    $form->advanced->setProperty('collapsible', TRUE);
    $form->advanced->setProperty('collapsed', TRUE);
    $form->basic->submit->setProperty('icon', 'fa-search');
  }

}
