<?php

namespace Drupal\aeon\Plugin\Preprocess;

use Drupal\aeon\Utility\Variables;

/**
 * Pre-processes variables for the "html" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @AeonPreprocess("html")
 */
class Html extends PreprocessBase {

  /**
   * {@inheritdoc}
   */
  public function preprocessVariables(Variables $variables) {
    $path = \Drupal::service('path.current')->getPath();
    if (in_array($path, [
      '/user/login',
      '/user/register',
      '/user/password',
    ])) {
      $variables->addClass('auth');
    }
  }

}
