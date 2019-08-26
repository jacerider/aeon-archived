<?php

namespace Drupal\aeon\Plugin\Preprocess;

use Drupal\aeon\Utility\Variables;

/**
 * Pre-processes variables for the "commerce_product_attribute_value" hook.
 *
 * @ingroup plugins_preprocess
 *
 * @AeonPreprocess("commerce_product_attribute_value")
 */
class CommerceProductAttributeValue extends PreprocessBase {
  use PreprocessEntityTrait;

  /**
   * {@inheritdoc}
   */
  protected function preprocessVariables(Variables $variables) {
    $variables['view_mode'] = $variables['elements']['#view_mode'];
    $variables['product_attribute_value_type'] = $variables['product_attribute_value_entity']->bundle();
  }

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
    return $group->setPropertyName('product_attribute_value');
  }

}
