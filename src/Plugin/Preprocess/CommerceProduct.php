<?php

namespace Drupal\aeon\Plugin\Preprocess;

use Drupal\aeon\Utility\Variables;
use Drupal\node\NodeInterface;

/**
 * Pre-processes variables for the "commerce_product" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @AeonPreprocess("commerce_product")
 */
class CommerceProduct extends PreprocessBase {
  use PreprocessEntityTrait;

  /**
   * Create a new field group.
   *
   * @param array $attributes
   *   The attributes to add to the group wrapper.
   * @param int $weight
   *   The weight of the render array.
   */
  protected function addGroup(array $attributes = [], $weight = 0) {
    $group = new PreprocessEntityGroup($this->variables, $attributes, $weight);
    return $group->setPropertyName('product');
  }
  
}
