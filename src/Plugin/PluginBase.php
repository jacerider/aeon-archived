<?php
/**
 * @file
 * Contains \Drupal\aeon\Plugin\PluginBase.
 */

namespace Drupal\aeon\Plugin;

use Drupal\aeon\Aeon;

/**
 * Base class for an update.
 *
 * @ingroup utility
 */
class PluginBase extends \Drupal\Core\Plugin\PluginBase {

  /**
   * The currently set theme object.
   *
   * @var \Drupal\aeon\Theme
   */
  protected $theme;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    if (!isset($configuration['theme'])) {
      $configuration['theme'] = Aeon::getTheme();
    }
    $this->theme = $configuration['theme'];
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

}
