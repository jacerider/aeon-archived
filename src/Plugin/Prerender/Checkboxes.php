<?php

namespace Drupal\aeon\Plugin\Prerender;

use Drupal\aeon\Utility\Element;

/**
 * Pre-render callback for the "checkboxes" element type.
 *
 * @ingroup plugins_prerender
 *
 * @AeonPrerender("checkboxes")
 *
 * @see \Drupal\Core\Render\Element\Checkboxes::preRenderCheckboxes()
 */
class Checkboxes extends PrerenderBase {

  /**
   * {@inheritdoc}
   */
  public static function preRenderElement(Element $element) {
    // Add the element id as a class to the wrapper.
    $element->addClass($element->getProperty('name'));
  }

}
