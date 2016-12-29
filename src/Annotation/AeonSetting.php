<?php

namespace Drupal\aeon\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a AeonSetting annotation object.
 *
 * Plugin Namespace: "Plugin/Setting".
 *
 * @see \Drupal\aeon\Plugin\SettingInterface
 * @see \Drupal\aeon\Plugin\SettingManager
 * @see plugin_api
 *
 * @Annotation
 *
 * @Attributes({
 *
 * @Attribute("defaultValue", type = "mixed", required = true),
 *
 * @Attribute("type", type = "string", required = true),
 * })
 *
 * @ingroup plugins_setting
 */
class AeonSetting extends Plugin {

  /**
   * The setting's description.
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $description;

  /**
   * The setting's default value.
   *
   * @var mixed
   */
  public $defaultValue;

  /**
   * The setting's groups.
   *
   * @var \Drupal\Core\Annotation\Translation[]
   */
  public $groups = [];

  /**
   * The setting's title.
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $title;

  /**
   * The setting's type.
   *
   * @var string
   */
  public $type;

  /**
   * The setting's see references.
   *
   * @var array
   */
  public $see = [];

  /**
   * {@inheritdoc}
   */
  public function __construct($values) {
    if (!isset($values['groups'])) {
      $values['groups'] = ['general' => t('General')];
    }
    parent::__construct($values);
  }

}
