<?php

namespace Drupal\aeon\Plugin\Preprocess;

use Drupal\aeon\Utility\Variables;
use Drupal\Component\Utility\Html;

/**
 * Pre-processes variables for the "slick" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @AeonPreprocess("slick")
 */
class Slick extends PreprocessBase {

  /**
   * {@inheritdoc}
   */
  public function preprocessVariables(Variables $variables) {
    $element = $variables['element'];
    $settings = $element['#settings'];
    if (!empty($settings['field_name'])) {
      // Add field class to slick wrapper.
      $variables['attributes']['class'][] = 'field';
      $variables['attributes']['class'][] = Html::cleanCssIdentifier(str_replace('field_', '', $settings['field_name']));
    }
  }

}
