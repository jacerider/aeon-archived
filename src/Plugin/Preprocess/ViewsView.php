<?php

namespace Drupal\aeon\Plugin\Preprocess;

use Drupal\aeon\Utility\Variables;

/**
 * Pre-processes variables for the "views_view" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @AeonPreprocess("views_view")
 */
class ViewsView extends PreprocessBase {

  /**
   * {@inheritdoc}
   */
  public function preprocessVariables(Variables $variables) {
    $view = $variables['view'];
    $view_array = $variables['view_array'];

    $variables['title_tag'] = 'div';

    if (isset($view_array['#title']) && $view_array['#display_id'] == 'default') {
      // Allow title insertion even for 'default' views.
      $variables['title'] = $view_array['#title'];
    }

    if ($view->ajaxEnabled()) {
      $variables->addClass('js-view-dom-id-' . $variables['dom_id']);
    }
    if (empty($variables->getClasses())) {
      $variables->removeAttribute('class');
    }
  }

}
