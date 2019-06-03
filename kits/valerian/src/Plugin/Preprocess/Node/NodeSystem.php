<?php

namespace Drupal\aeon_kit\Plugin\Preprocess\Node;

use Drupal\aeon\Utility\Variables;

/**
 * Pre-processes variables for the "node" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @AeonPreprocess("node__system")
 */
class NodeSystem extends NodeBase {

  /**
   * {@inheritdoc}
   */
  public function preprocessVariables(Variables $variables) {
    parent::preprocessVariables($variables);

    $node = $variables['node'];
    $view_mode = $variables['elements']['#view_mode'];
    $camel = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $view_mode)));
    $methods = [
      'preprocessVariables' . $node->id(),
      'preprocessVariables' . $camel . $node->id(),
    ];

    $variables['content']['field_body']['#weight'] = -100;

    foreach ($methods as $method) {
      if (method_exists($this, $method)) {
        $this->{$method}($variables);
      }
    }
  }

}
