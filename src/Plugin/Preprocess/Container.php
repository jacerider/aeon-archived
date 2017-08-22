<?php

namespace Drupal\aeon\Plugin\Preprocess;

use Drupal\aeon\Utility\Variables;

/**
 * Pre-processes variables for the "container" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @AeonPreprocess("container")
 */
class Container extends PreprocessBase {

  /**
   * {@inheritdoc}
   */
  public function preprocessVariables(Variables $variables) {
    // Views module wrapps all views in a container and hardcodes a class to
    // it. We don't want this div so we check for the class and remove it
    // allowing the container.html.twig template to skip the wrapper.
    $variables->removeClass('views-element-container');
    $variables['show_wrapper'] = !empty(array_filter($variables->getAttributes()->getArrayCopy()));
    // Allow settings the wrapper tag.
    if ($tag = $variables->element->getProperty('tag')) {
      $variables['tag'] = $tag;
    }
  }

}
