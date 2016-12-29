<?php

namespace Drupal\aeon\Plugin\Preprocess;

use Drupal\aeon\Utility\Variables;

/**
 * Pre-processes variables for the "block" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @AeonPreprocess("block")
 */
class Block extends PreprocessBase implements PreprocessInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocessVariables(Variables $variables) {
    // Add id to template.
    $variables['id'] = '';
    if (isset($variables['elements']['#id'])) {
      $variables['id'] = str_replace('_', '-', $variables['elements']['#id']);
      unset($variables['attributes']['id']);
      unset($variables['title_attributes']['id']);
    }
  }

}
