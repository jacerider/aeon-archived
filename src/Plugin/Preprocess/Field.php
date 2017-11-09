<?php

namespace Drupal\aeon\Plugin\Preprocess;

use Drupal\aeon\Utility\Variables;
use Drupal\Component\Utility\NestedArray;

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

    // Allow properties to be provided with the element.
    foreach (['tag', 'attributes'] as $key) {
      if (isset($element['#' . $key])) {
        if (is_array($element['#' . $key])) {
          $variables[$key] = !empty($variables[$key]) ? $variables[$key] : [];
          $variables[$key] = NestedArray::mergeDeep($variables[$key], $element['#' . $key]);
        }
        else {
          $variables[$key] = $element['#' . $key];
        }
      }
    }

    if ($is_multiple && $formatter == 'entity_reference_label') {
      // Entity reference label multi-value fields should be wrapped in a
      // span tag.
      $this->setContentTag('span');
    }
  }

}
