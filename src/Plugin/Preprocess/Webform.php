<?php

namespace Drupal\aeon\Plugin\Preprocess;

use Drupal\aeon\Utility\Variables;
use Drupal\Component\Utility\Html;

/**
 * Pre-processes variables for the "webform" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @AeonPreprocess("webform")
 */
class Webform extends PreprocessBase {

  /**
   * {@inheritdoc}
   */
  public function preprocessVariables(Variables $variables) {
    $id = $variables->element->getProperty('webform_id');
    $variables->addClass('webform');
    $variables->addClass('form-' . Html::cleanCssIdentifier($id));
  }

}
