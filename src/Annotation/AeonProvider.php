<?php

namespace Drupal\aeon\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a AeonProvider annotation object.
 *
 * Plugin Namespace: "Plugin/Provider".
 *
 * @see \Drupal\aeon\Plugin\ProviderInterface
 * @see \Drupal\aeon\Plugin\ProviderManager
 * @see \Drupal\aeon\Theme::getProviders()
 * @see \Drupal\aeon\Theme::getProvider()
 * @see plugin_api
 *
 * @Annotation
 *
 * @ingroup plugins_provider
 */
class AeonProvider extends Plugin {

  /**
   * An API URL used to retrieve data for the provider.
   *
   * @var string
   */
  protected $api = '';

  /**
   * An array of CSS assets.
   *
   * @var array
   */
  protected $css = [];

  /**
   * A description about the provider.
   *
   * @var string
   */
  protected $description = '';

  /**
   * A flag determining whether or not the API request has failed.
   *
   * @var bool
   */
  protected $error = FALSE;

  /**
   * A flag determining whether or not data has been manually imported.
   *
   * @var bool
   */
  protected $imported = FALSE;

  /**
   * An array of JavaScript assets.
   *
   * @var array
   */
  protected $js = [];

  /**
   * A human-readable label.
   *
   * @var string
   */
  protected $label = '';

  /**
   * An associative array of minified CSS and JavaScript assets.
   *
   * @var array
   */
  protected $min = ['css' => [], 'js' => []];

  /**
   * An array of themes supported by the provider.
   *
   * @var array
   */
  protected $themes = [];

  /**
   * An array of versions supported by the provider.
   *
   * @var array
   */
  protected $versions = [];

}
