<?php
/**
 * @file
 * Contains \Drupal\aeon\Plugin\FormManager.
 */

namespace Drupal\aeon\Plugin;

use Drupal\aeon\Theme;

/**
 * Manages discovery and instantiation of Aeon form alters.
 *
 * @ingroup plugins_form
 */
class FormManager extends PluginManager {

  /**
   * Constructs a new \Drupal\aeon\Plugin\FormManager object.
   *
   * @param \Drupal\aeon\Theme $theme
   *   The theme to use for discovery.
   */
  public function __construct(Theme $theme) {
    parent::__construct($theme, 'Plugin/Form', 'Drupal\aeon\Plugin\Form\FormInterface', 'Drupal\aeon\Annotation\AeonForm');
    $this->setCacheBackend(\Drupal::cache('discovery'), 'theme:' . $theme->getName() . ':form', $this->getCacheTags());
  }

}
