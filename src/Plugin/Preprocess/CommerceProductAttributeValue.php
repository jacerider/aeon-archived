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

}
