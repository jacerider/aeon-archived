<?php

namespace Drupal\aeon\Plugin\Preprocess;

use Drupal\aeon\Utility\Variables;
use Drupal\Core\Template\Attribute;
use Drupal\Core\Render\Element;
use Drupal\Core\Cache\CacheableMetadata;

/**
 * A trait that provides dialog utilities.
 */
class PreprocessRegionGroup {

  /**
   * The Variables object.
   *
   * @var \Drupal\aeon\Utility\Variables
   */
  protected $variables;

  /**
   * The Attribute object.
   *
   * @var \Drupal\Core\Template\Attribute
   */
  protected $attributes;

  /**
   * The weight of the group.
   *
   * @var int
   */
  protected $weight;

  /**
   * The array of block names to add to the group.
   *
   * @var array
   */
  protected $blocks = [];

  /**
   * The array of subgroups to add to the group.
   *
   * @var array
   */
  protected $subGroups = [];

  /**
   * The ID of this instance.
   *
   * @var string
   */
  protected $id = '';

  /**
   * A static counter used to set group key.
   *
   * @var int
   */
  protected static $count = 0;

  /**
   * Class constructor.
   *
   * @param \Drupal\aeon\Utility\Variables $variables
   *   The variables of the preprocess element.
   * @param array $attributes
   *   The attributes to add to the group wrapper.
   * @param int $weight
   *   The weight of the render array.
   */
  public function __construct(Variables $variables, array $attributes = [], $weight = 0) {
    $this->variables = $variables;
    $this->element = $this->variables['elements'];
    // We need to break up each block into it's own render element.
    if (is_a($this->variables['content'], '\Drupal\Core\Render\Markup')) {
      $this->variables['content'] = [];
      foreach (Element::children($this->element) as $id) {
        $this->variables['content'][$id]['#markup'] = isset($this->element[$id]['#markup']) ? $this->element[$id]['#markup'] : '';
        $this->variables['content'][$id]['#cache'] = isset($this->element[$id]['#cache']) ? $this->element[$id]['#cache'] : '';
      }
    }
    $this->attributes = new Attribute($attributes);
    $this->weight = $weight;
    self::$count++;
  }

  /**
   * The ID to use as the render array identifier.
   *
   * @param string $identifier
   *   The identifier to set as the id.
   */
  public function setId($identifier) {
    $this->id = Html::cleanCssIdentifier($identifier);
    return $this;
  }

  /**
   * Get the ID of this isntance.
   */
  public function getId() {
    return $this->id ? $this->id : 'aeon_group_' . self::$count;
  }

  /**
   * Add a block to the group.
   *
   * @param string $block_name
   *   The blockname to add to the group.
   */
  public function addBlock($block_name) {
    if (isset($this->variables['content'][$block_name])) {
      if (!empty($this->variables['content'][$block_name]['#markup'])) {
        $this->blocks[$block_name] = $this->variables['content'][$block_name];
        unset($this->variables['content'][$block_name]);
      }
    }
    return $this;
  }

  /**
   * Add blocks to the output.
   *
   * @param array $block_names
   *   An array of block names.
   */
  public function addBlocks(array $block_names) {
    foreach ($block_names as $block_name) {
      $this->addBlock($block_name);
    }
    return $this;
  }

  /**
   * Add a subgroup to the group.
   *
   * @param array $attributes
   *   The attributes to add to the group wrapper.
   * @param int $weight
   *   The weight of the render array.
   */
  public function addSubGroup(array $attributes = [], $weight = 0) {
    $subgroup = new PreprocessRegionGroup($this->variables, $attributes, $weight);
    $this->subGroups[] = $subgroup;
    return $subgroup;
  }

  /**
   * Adds classes or merges them on to array of existing CSS classes.
   *
   * @param string|array ...
   *   CSS classes to add to the class attribute array.
   *
   * @return $this
   */
  public function addClass($classes) {
    $this->attributes->addClass($classes);
    return $this;
  }

  /**
   * Sets values for an attribute key.
   *
   * @param string $attribute
   *   Name of the attribute.
   * @param string|array $value
   *   Value(s) to set for the given attribute key.
   *
   * @return $this
   */
  public function setAttribute($attribute, $value) {
    $this->attributes->setAttribute($attribute, $value);
    return $this;
  }

  /**
   * Set the weight of the group.
   *
   * @param int $weight
   *   The group weight as a numeric value.
   */
  public function setWeight(int $weight) {
    $this->weight = $weight;
    return $this;
  }

  /**
   * Build the renderable output of the group.
   *
   * @param bool $add_to_content
   *   If TRUE the renderable content will be added to the content of the
   *   region.
   *
   * @return array|null
   *   Will return the renderable array.
   */
  public function render($add_to_content = TRUE) {
    $render = [];
    $cacheable_metadata = new CacheableMetadata();
    if (empty($this->blocks)) {
      return $render;
    }
    foreach ($this->blocks as $block_name => $content) {
      $render[$block_name] = $content;
      $cacheable_metadata = $cacheable_metadata->merge(CacheableMetadata::createFromRenderArray($render[$block_name]));
    }
    foreach ($this->subGroups as $key => $subgroup) {
      $render['aeon_subgroup_' . $key] = $subgroup->render(FALSE);
      $cacheable_metadata = $cacheable_metadata->merge(CacheableMetadata::createFromRenderArray($render['aeon_subgroup_' . $key]));
    }
    if (!empty($render)) {
      $render += [
        '#type' => 'container',
        '#aeon_group' => TRUE,
        '#attributes' => $this->attributes->toArray(),
        '#weight' => $this->weight,
      ];
      $render['#attributes']['class'][] = 'group';
      if ($add_to_content) {
        $this->variables['content'][$this->getId()] = $render;
      }
      $cacheable_metadata->applyTo($render);
      return $render;
    }
  }

}
