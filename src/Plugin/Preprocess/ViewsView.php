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
class ViewsView extends PreprocessBase implements PreprocessInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocessVariables(Variables $variables) {
    $view = $variables['view'];

    if ($view->ajaxEnabled()) {
      $variables->addClass('js-view-dom-id-' . $variables['dom_id']);
    }
    if (empty($variables->getClasses())) {
      $variables->removeAttribute('class');
    }
  }

}
