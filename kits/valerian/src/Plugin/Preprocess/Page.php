<?php

namespace Drupal\ash\Plugin\Preprocess;

use Drupal\aeon\Plugin\Preprocess\Page as AeonPage;
use Drupal\aeon\Utility\Variables;

/**
 * Pre-processes variables for the "page" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @AeonPreprocess("page")
 */
class Page extends AeonPage {

  /**
   * {@inheritdoc}
   */
  public function preprocessVariables(Variables $variables) {
    parent::preprocessVariables($variables);
    $route_name = $this->getRouteMatch()->getRouteName();
    switch ($route_name) {
      case 'user.login':
      case 'user.register':
      case 'user.pass':
      case 'user.reset.form':
        $variables->addClass('login');
        break;
    }
  }

}
