<?php

namespace Drupal\aeon\Plugin\Form;

use Drupal\aeon\Annotation\AeonForm;
use Drupal\aeon\Utility\Element;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hook_form_FORM_ID_alter().
 *
 * @ingroup plugins_form
 *
 * @AeonForm("search_block_form")
 */
class SearchBlockForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function alterFormElement(Element $form, FormStateInterface $form_state, $form_id = NULL) {
    $form->actions->submit->setProperty('icon', 'fa-search');
    $form->actions->submit->setProperty('icon_only', TRUE);
    $form->keys->setProperty('input_group_button', TRUE);
  }

}
