<?php

namespace Drupal\aeon\Plugin\Preprocess;

use Drupal\aeon\Utility\Variables;
use Drupal\Core\Template\Attribute;
use Drupal\Component\Utility\Html;
use Drupal\Core\Render\Element;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Url;

/**
 * A trait that provides dialog utilities.
 */
class PreprocessEntityGroup {

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
   * The Entity object.
   *
   * @var \Drupal\Core\Entity\EntityInterface
   */
  protected $entity;

  /**
   * The property that holds the renderable content.
   *
   * @var string
   */
  protected $propertyName = 'content';

  /**
   * The HTML tag of the group.
   *
   * @var string
   */
  protected $tag = 'div';

  /**
   * The weight of the group.
   *
   * @var int
   */
  protected $weight;

  /**
   * The array of field names to add to the group.
   *
   * @var array
   */
  protected $fields = [];

  /**
   * The array of subgroups to add to the group.
   *
   * @var array
   */
  protected $subGroups = [];

  /**
   * The ID of this instance.
   *
   * @var string|int
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
    $this->attributes = new Attribute($attributes);
    $this->entity = $this->variables->element['#' . $this->variables->element['#entity_type']];
    $this->weight = $weight;
    $this->setId(self::$count);
    self::$count++;
  }

  /**
   * Set the property that holds the renderable content.
   *
   * @return $this
   */
  public function setPropertyName($property_name) {
    $this->propertyName = $property_name;
    return $this;
  }

  /**
   * The ID to use as the render array identifier.
   *
   * @param string $identifier
   *   The identifier to set as the id.
   *
   * @return $this
   */
  public function setId($identifier) {
    $this->id = $identifier;
    return $this;
  }

  /**
   * Get the ID of this isntance.
   */
  public function getId() {
    return 'aeon_group_' . $this->id;
  }

  /**
   * Add a field to the group.
   *
   * @param string $field_name
   *   The fieldname to add to the group.
   *
   * @return $this
   */
  public function addField($field_name) {
    if ($field_name == 'label' && isset($this->variables[$field_name])) {
      $this->addLabel();
    }
    if (isset($this->variables[$this->propertyName][$field_name])) {
      // If field is an actual entity field, check if it has a value.
      if (isset($this->entity->{$field_name}) && $this->entity->{$field_name}->isEmpty() && empty($this->variables[$this->propertyName][$field_name]['#aeon_force'])) {
        return $this;
      }
      $this->fields[$field_name] = $this->variables[$this->propertyName][$field_name];
      unset($this->variables[$this->propertyName][$field_name]);
    }
    return $this;
  }

  /**
   * Add fields to the output.
   *
   * @param array $field_names
   *   An array of field names.
   *
   * @return $this
   */
  public function addFields(array $field_names) {
    foreach ($field_names as $field_name) {
      $this->addField($field_name);
    }
    return $this;
  }

  /**
   * Add the entity label to the group.
   *
   * @param int $weight
   *   The weight of the render array.
   *
   * @return $this
   */
  public function addLabel($weight = 0) {
    if (isset($this->variables['label']) && !isset($this->variables[$this->propertyName]['label'])) {
      $this->variables['title_hide'] = TRUE;
      $this->variables[$this->propertyName]['label'] = $this->variables['label'];
      $this->variables[$this->propertyName]['label']['#weight'] = $weight;
      $this->variables[$this->propertyName]['label']['#tag'] = $this->variables['title_tag'];
      $this->variables[$this->propertyName]['label']['#printed'] = FALSE;
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
    $subgroup = new PreprocessEntityGroup($this->variables, $attributes, $weight);
    $subgroup->setPropertyName($this->propertyName);
    $this->subGroups[] = $subgroup;
    return $subgroup;
  }

  /**
   * Add remaining fields.
   *
   * @param array $exclude
   *   An array of field ids to exclude.
   *
   * @return $this
   */
  public function addRemaining(array $exclude = []) {
    $field_names = [];
    foreach (Element::children($this->variables[$this->propertyName]) as $field_id) {
      $field = $this->variables[$this->propertyName][$field_id];
      if (empty($field['#aeon_group']) && !in_array($field_id, $exclude)) {
        $field_names[] = $field_id;
      }
    }
    if (!empty($field_names)) {
      $this->addFields($field_names);
    }
    return $this;
  }

  /**
   * Set groups as link given a Link field.
   *
   * @var string
   *   The field name to fetch a URI from.
   *
   * @return $this
   */
  public function setAsLink($field_name = NULL) {
    if ($field_name) {
      if (isset($this->entity->{$field_name}) && !$this->entity->{$field_name}->isEmpty() && $uri = $this->entity->{$field_name}->uri) {
        $this->setAttribute('href', Url::fromUri($uri)->toString());
        $this->setAttribute('rel', 'bookmark');
      }
    }
    else {
      $this->setAttribute('href', $this->variables['url']);
    }
    $this->setTag('a');
    $this->setAttribute('rel', 'bookmark');
    return $this;
  }

  /**
   * Set the HTML tag of the group.
   *
   * @param string $tag
   *   The group weight as a numeric value.
   *
   * @return $this
   */
  public function setTag($tag) {
    $this->tag = $tag;
    return $this;
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
   *   entity.
   *
   * @return array|null
   *   Will return the renderable array.
   */
  public function render($add_to_content = TRUE) {
    $render = [];
    $cacheable_metadata = new CacheableMetadata();
    foreach ($this->fields as $field_name => $content) {
      $render[$field_name] = $content;
      $cacheable_metadata = $cacheable_metadata->merge(CacheableMetadata::createFromRenderArray($render[$field_name]));
    }
    foreach ($this->subGroups as $key => $subgroup) {
      $render['aeon_subgroup_' . $key] = $subgroup->render(FALSE);
      $cacheable_metadata = $cacheable_metadata->merge(CacheableMetadata::createFromRenderArray($render['aeon_subgroup_' . $key]));
    }
    if (!empty($render)) {
      $render += [
        '#type' => 'container',
        '#aeon_group' => TRUE,
        '#tag' => $this->tag,
        '#attributes' => $this->attributes->toArray(),
        '#weight' => $this->weight,
      ];
      $render['#attributes']['class'][] = 'group';
      if ($add_to_content) {
        $this->variables[$this->propertyName][$this->getId()] = $render;
      }
      $cacheable_metadata->applyTo($render);
    }
    return $render;
  }

}
