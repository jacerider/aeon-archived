<?php

namespace Drupal\aeon\Plugin\Prerender;

use Drupal\aeon\Utility\Element;

/**
 * Defines the interface for an object oriented preprocess plugin.
 *
 * @ingroup plugins_prerender
 */
class PrerenderBase implements PrerenderInterface {

  /**
   * {@inheritdoc}
   */
  public static function preRender(array $element) {
    static::preRenderElement(Element::create($element));
    return $element;
  }

  /**
   * Pre-render element callback.
   *
   * @param \Drupal\aeon\Utility\Element $element
   *   The element object.
   */
  public static function preRenderElement(Element $element) {}

}
