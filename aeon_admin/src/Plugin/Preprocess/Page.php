<?php

namespace Drupal\aeon_admin\Plugin\Preprocess;

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
    $variables->addClass('exo-reset exo-font');
  }

}
