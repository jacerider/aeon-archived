<?php

namespace Drupal\aeon\Plugin;

use Drupal\aeon\Theme;

/**
 * Manages discovery and instantiation of Aeon preprocess hooks.
 *
 * @ingroup plugins_preprocess
 */
class PreprocessManager extends PluginManager {

  /**
   * Constructs a new \Drupal\aeon\Plugin\PreprocessManager object.
   *
   * @param \Drupal\aeon\Theme $theme
   *   The theme to use for discovery.
   */
  public function __construct(Theme $theme) {
    parent::__construct($theme, 'Plugin/Preprocess', 'Drupal\aeon\Plugin\Preprocess\PreprocessInterface', 'Drupal\aeon\Annotation\AeonPreprocess');
    $this->setCacheBackend(\Drupal::cache('discovery'), 'theme:' . $theme->getName() . ':preprocess', $this->getCacheTags());
  }

}
