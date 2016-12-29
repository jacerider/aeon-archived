<?php

namespace Drupal\aeon\Annotation;

use Drupal\aeon\Aeon;
use Drupal\Component\Annotation\AnnotationInterface;
use Drupal\Component\Annotation\PluginID;

/**
 * Defines a Plugin annotation object that just contains an ID.
 *
 * @Annotation
 *
 * @ingroup utility
 */
class PluginCallback extends PluginID {

  /**
   * The plugin ID.
   *
   * When an annotation is given no key, 'value' is assumed by Doctrine.
   *
   * @var string
   */
  public $value;

  /**
   * Flag that determines how to add the plugin to a callback array.
   *
   * Must be one of the following constants:
   *   - \Drupal\aeon\Aeon::CALLBACK_APPEND
   *   - \Drupal\aeon\Aeon::CALLBACK_PREPEND
   *   - \Drupal\aeon\Aeon::CALLBACK_REPLACE_APPEND
   *   - \Drupal\aeon\Aeon::CALLBACK_REPLACE_PREPEND
   * Use with @ AeonConstant annotation.
   *
   * @var \Drupal\aeon\Annotation\AeonConstant
   *
   * @see \Drupal\aeon\Aeon::addCallback()
   */
  public $action = Aeon::CALLBACK_APPEND;

  /**
   * A callback to replace.
   *
   * @var string
   */
  public $replace = FALSE;

  /**
   * {@inheritdoc}
   */
  public function get() {
    $definition = parent::get();
    $parent_properties = array_keys($definition);
    $parent_properties[] = 'value';

    // Merge in the defined properties.
    $reflection = new \ReflectionClass($this);
    foreach ($reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
      $name = $property->getName();
      if (in_array($name, $parent_properties)) {
        continue;
      }
      $value = $property->getValue($this);
      if ($value instanceof AnnotationInterface) {
        $value = $value->get();
      }
      $definition[$name] = $value;
    }

    return $definition;
  }

}
