<?php
/**
 * @file
 * Contains \Drupal\aeon\Plugin\PrerenderManager.
 */

namespace Drupal\aeon\Plugin;

use Drupal\aeon\Theme;
use Drupal\aeon\Utility\Element;
use Drupal\Core\Security\TrustedCallbackInterface;

/**
 * Manages discovery and instantiation of Aeon pre-render callbacks.
 *
 * @ingroup plugins_prerender
 */
class PrerenderManager extends PluginManager implements TrustedCallbackInterface {

  /**
   * Constructs a new \Drupal\aeon\Plugin\PrerenderManager object.
   *
   * @param \Drupal\aeon\Theme $theme
   *   The theme to use for discovery.
   */
  public function __construct(Theme $theme) {
    parent::__construct($theme, 'Plugin/Prerender', 'Drupal\aeon\Plugin\Prerender\PrerenderInterface', 'Drupal\aeon\Annotation\AeonPrerender');
    $this->setCacheBackend(\Drupal::cache('discovery'), 'theme:' . $theme->getName() . ':prerender', $this->getCacheTags());
  }

  /**
   * Pre-render render array element callback.
   *
   * @param array $element
   *   The render array element.
   *
   * @return array
   *   The modified render array element.
   */
  public static function preRender(array $element) {
    if (!empty($element['#aeon_ignore_pre_render'])) {
      return $element;
    }

    $e = Element::create($element);

    if ($e->isType('machine_name')) {
      $e->addClass('form-inline', 'wrapper_attributes');
    }

    // Add smart descriptions to the element, if necessary.
    $e->smartDescription();

    return $element;
  }

  /**
   * {@inheritDoc}
   */
  public static function trustedCallbacks() {
    return ['preRender'];
  }

}
