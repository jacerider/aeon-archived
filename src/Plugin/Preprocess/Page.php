<?php

namespace Drupal\aeon\Plugin\Preprocess;

use Drupal\aeon\Utility\Variables;

/**
 * Pre-processes variables for the "page" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @AeonPreprocess("page")
 */
class Page extends PreprocessBase {

  /**
   * {@inheritdoc}
   */
  public function preprocessVariables(Variables $variables) {

    $route_name = $this->getRouteMatch()->getRouteName();
    switch ($route_name) {
      case 'user.login':
      case 'user.register':
      case 'user.pass':
      case 'user.reset.form':
        $variables->addClass('login');
        break;
    }

    $variables['logo'] = [
      '#theme' => 'image',
      '#uri' => $variables['base_path'] . $variables['directory'] . '/logo.svg',
      '#attributes' => [
        'class' => [
          'site-logo',
        ],
      ],
    ];

    $route_name = $this->getRouteMatch()->getRouteName();
    switch ($route_name) {
      case 'search.view_node_search':
      case 'search.view_user_search':
        $variables->addClass('search-results');
        break;
    }
  }

}
