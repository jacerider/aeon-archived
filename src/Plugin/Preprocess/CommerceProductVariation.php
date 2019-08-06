<?php

namespace Drupal\aeon\Plugin\Preprocess;

use Drupal\aeon\Utility\Variables;

/**
 * Pre-processes variables for the "commerce_product" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @AeonPreprocess("commerce_product_variation")
 */
class CommerceProductVariation extends PreprocessBase {
  use PreprocessEntityTrait;

  /**
   * {@inheritdoc}
   */
  protected function preprocessVariables(Variables $variables) {
    $variables['view_mode'] = $variables['elements']['#view_mode'];
    $variables['product_variation_type'] = $variables['product_variation_entity']->bundle();
  }

  /**
   * Set element as link to title.
   */
  protected function setAsLink() {
    $this->variables['tag'] = 'a';
    $this->variables['attributes']['href'] = $this->variables['product_variation_entity']->toUrl('canonical')->toString();
    $this->variables['attributes']['rel'] = 'bookmark';
  }

}
