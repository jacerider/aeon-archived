<?php

namespace Drupal\aeon\Plugin;

use Drupal\aeon\Plugin\Provider\ProviderInterface;
use Drupal\aeon\Theme;

/**
 * Manages discovery and instantiation of Aeon CDN providers.
 *
 * @ingroup plugins_provider
 */
class ProviderManager extends PluginManager {
  /**
   * The base file system path for CDN providers.
   *
   * @var string
   */
  const FILE_PATH = 'public://aeon/provider';

  /**
   * Constructs a new \Drupal\aeon\Plugin\ProviderManager object.
   *
   * @param \Drupal\aeon\Theme $theme
   *   The theme to use for discovery.
   */
  public function __construct(Theme $theme) {
    parent::__construct($theme, 'Plugin/Provider', 'Drupal\aeon\Plugin\Provider\ProviderInterface', 'Drupal\aeon\Annotation\AeonProvider');
    $this->setCacheBackend(\Drupal::cache('discovery'), 'theme:' . $theme->getName() . ':provider', $this->getCacheTags());
  }

  /**
   * {@inheritdoc}
   */
  public function processDefinition(&$definition, $plugin_id) {
    parent::processDefinition($definition, $plugin_id);
    /** @var ProviderInterface $provider */
    $provider = new $definition['class'](['theme' => $this->theme], $plugin_id, $definition);
    $provider->processDefinition($definition, $plugin_id);
  }

}
