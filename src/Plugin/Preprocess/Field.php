<?php

namespace Drupal\aeon\Plugin\Preprocess;

use Drupal\aeon\Utility\Variables;

/**
 * Pre-processes variables for the "field" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @AeonPreprocess("field")
 */
class Field extends PreprocessBase {

  /**
   * {@inheritdoc}
   */
  public function preprocessVariables(Variables $variables) {
    $element = $variables['element'];
    $is_multiple = $element['#is_multiple'];
    $formatter = $element['#formatter'];
    if ($is_multiple && $formatter == 'entity_reference_label') {
      // Entity reference label multi-value fields should be wrapped in a
      // span tag.
      $this->setContentTag('span');
    }
  }

}
