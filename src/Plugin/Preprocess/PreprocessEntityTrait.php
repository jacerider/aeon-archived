<?php

namespace Drupal\aeon\Plugin\Preprocess;

use Drupal\Core\Render\Element;

/**
 * A trait that provides dialog utilities.
 */
trait PreprocessEntityTrait {

  /**
   * Create a new field group.
   *
   * @param array $attributes
   *   The attributes to add to the group wrapper.
   * @param int $weight
   *   The weight of the render array.
   */
  protected function addGroup(array $attributes = [], $weight = 0) {
    return new PreprocessEntityGroup($this->variables, $attributes);
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
    $field_names = [];
    foreach (Element::children($this->variables['content']) as $field_id) {
      $field = $this->variables['content'][$field_id];
      if (empty($field['#aeon_group'])) {
        $field_names[] = $field_id;
      }
    }
    return $this->addGroup($attributes)->addFields($field_names);
  }

}
