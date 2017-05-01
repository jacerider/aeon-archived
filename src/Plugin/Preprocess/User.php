<?php

namespace Drupal\aeon\Plugin\Preprocess;

use Drupal\aeon\Utility\Variables;

/**
 * Pre-processes variables for the "user" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @AeonPreprocess("user")
 */
class User extends PreprocessBase {

  /**
   * {@inheritdoc}
   */
  public function preprocessVariables(Variables $variables) {
    // Add view mode to user.html.twig.
    $variables['view_mode'] = $variables['elements']['#view_mode'];
  }

}
