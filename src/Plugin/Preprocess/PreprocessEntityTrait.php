<?php

namespace Drupal\aeon\Plugin\Preprocess;

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
    return new PreprocessEntityGroup($this->variables, $attributes, $weight);
  }

  /**
   * Group fields.
   *
   * @param array $field_names
   *   An array of field names.
   * @param array $attributes
   *   An array of attributes.
   * @param int $weight
   *   The weight of the render array.
   */
  protected function groupFields(array $field_names, array $attributes = [], $weight = 0) {
    return $this->addGroup($attributes, $weight)->addFields($field_names);
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
    return $this->addGroup($attributes, $weight)->addRemaining();
  }

}
