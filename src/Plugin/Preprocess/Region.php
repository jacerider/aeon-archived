<?php

namespace Drupal\aeon\Plugin\Preprocess;

use Drupal\aeon\Utility\Variables;
use Drupal\Core\Render\Element;

/**
 * Pre-processes variables for the "region" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @AeonPreprocess("region")
 */
class Region extends PreprocessBase {

  /**
   * {@inheritdoc}
   */
  public function preprocessVariables(Variables $variables) {
    $region = $variables['elements']['#region'];

    // Alter based on region id.
    switch ($region) {
      case 'header':
        $this->setTag('header');
        $variables->setAttribute('role', 'banner');
        $this->setContentTag('div', ['class' => ['header-inner']]);
        break;

      case 'footer':
        $this->setTag('footer');
        $variables->setAttribute('role', 'contentinfo');
        break;

      case 'sidebar_first':
      case 'sidebar_second':
        $this->setTag('aside');
        $variables->setAttribute('role', 'complementary');
        break;
    }
  }

  /**
   * Create a new block group.
   *
   * @param array $attributes
   *   The attributes to add to the group wrapper.
   * @param int $weight
   *   The weight of the render array.
   */
  protected function addGroup(array $attributes = [], $weight = 0) {
    return new PreprocessRegionGroup($this->variables, $attributes, $weight);
  }

  /**
   * Group any fields not grouped into their own group.
   *
   * @param array $attributes
   *   An array of attributes.
   * @param int $weight
   *   The weight of the render array.
   */
  protected function groupRemaining(array $attributes = [], $weight = 0) {
    $block_names = [];
    foreach (Element::children($this->variables['content']) as $field_id) {
      $field = $this->variables['content'][$field_id];
      if (empty($field['#aeon_group'])) {
        $block_names[] = $field_id;
      }
    }
    return $this->addGroup($attributes)->addBlocks($block_names);
  }

}
