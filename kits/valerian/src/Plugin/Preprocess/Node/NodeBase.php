<?php

namespace Drupal\aeon_kit\Plugin\Preprocess\Node;

use Drupal\aeon\Plugin\Preprocess\Node as AeonNode;
use Drupal\aeon\Utility\Variables;

/**
 * Base node preprocessing.
 */
abstract class NodeBase extends AeonNode {

  /**
   * {@inheritdoc}
   */
  public function preprocessVariables(Variables $variables) {
    parent::preprocessVariables($variables);
    $node = $variables['node'];
    $view_mode = $variables['elements']['#view_mode'];
    $camel = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $view_mode)));
    $methods = [
      'preprocessVariables' . $camel,
    ];
    foreach ($methods as $method) {
      if (method_exists($this, $method)) {
        $this->{$method}($variables);
      }
    }
  }

}
