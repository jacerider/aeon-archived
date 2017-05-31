<?php

namespace Drupal\aeon\Plugin\Preprocess;

use Drupal\aeon\Utility\Element;
use Drupal\aeon\Utility\Variables;

/**
 * Pre-processes variables for the "input" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @AeonPreprocess("input")
 */
class Input extends PreprocessBase implements PreprocessInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocessElement(Element $element, Variables $variables) {
    $element->map(['id', 'name', 'value', 'type']);

    // Autocomplete.
    if ($route = $element->getProperty('autocomplete_route_name')) {
      $variables['autocomplete'] = TRUE;
    }
  }

}
