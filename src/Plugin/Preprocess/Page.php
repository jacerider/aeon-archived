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
    $variables['logo'] = [
      '#theme' => 'image',
      '#uri' => $variables['base_path'] . $variables['directory'] . '/logo.svg',
      '#attributes' => [
        'class' => [
          'site-logo',
        ],
      ],
    ];
  }

}
