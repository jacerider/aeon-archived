<?php

namespace Drupal\aeon\Plugin\Preprocess;

use Drupal\aeon\Utility\Variables;

/**
 * Pre-processes variables for the "region" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @AeonPreprocess("region")
 */
class Region extends PreprocessBase {

  /**
   * {@inheritdoc}
   */
  public function preprocessVariables(Variables $variables) {
    $region = $variables['elements']['#region'];

    // Alter based on region id.
    switch ($region) {
      case 'header':
        $variables['tag'] = 'header';
        $variables->setAttribute('role', 'banner');
        break;

      case 'footer':
        $variables['tag'] = 'footer';
        $variables->setAttribute('role', 'contentinfo');
        break;

      case 'sidebar_first':
      case 'sidebar_second':
        $variables['tag'] = 'aside';
        $variables->setAttribute('role', 'complementary');
        break;
    }
  }

}
