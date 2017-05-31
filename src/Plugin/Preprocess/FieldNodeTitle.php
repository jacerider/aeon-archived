<?php

namespace Drupal\aeon\Plugin\Preprocess;

use Drupal\aeon\Utility\Element;
use Drupal\aeon\Utility\Variables;

/**
 * Pre-processes variables for the "field__node__title" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @AeonPreprocess("field__node__title")
 */
class FieldNodeTitle extends PreprocessBase implements PreprocessInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocessElement(Element $element, Variables $variables) {
    // Map tag if set on element.
    $variables->map(['tag']);
    // Set default tag if not set from element.
    $variables['tag'] = !empty($variables['tag']) ? $variables['tag'] : 'span';
    $variables['attributes']['class'][] = 'title';
  }

}
