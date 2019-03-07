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
    $route_name = \Drupal::routeMatch()->getRouteName();
    switch ($route_name) {
      case 'user.login':
      case 'user.register':
      case 'user.pass':
      case 'user.reset.form':
        $suggestions[] = 'page__user__auth';
        break;
    }
  }

}
