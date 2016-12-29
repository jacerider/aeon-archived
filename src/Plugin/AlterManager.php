<?php
/**
 * @file
 * Contains \Drupal\aeon\Plugin\AlterManager.
 */

namespace Drupal\aeon\Plugin;

use Drupal\aeon\Theme;

/**
 * Manages discovery and instantiation of Aeon hook alters.
 *
 * @ingroup plugins_alter
 */
class AlterManager extends PluginManager {

  /**
   * Constructs a new \Drupal\aeon\Plugin\AlterManager object.
   *
   * @param \Drupal\aeon\Theme $theme
   *   The theme to use for discovery.
   */
  public function __construct(Theme $theme) {
    parent::__construct($theme, 'Plugin/Alter', 'Drupal\aeon\Plugin\Alter\AlterInterface', 'Drupal\aeon\Annotation\AeonAlter');
    $this->setCacheBackend(\Drupal::cache('discovery'), 'theme:' . $theme->getName() . ':alter', $this->getCacheTags());
  }

}
