<?php

namespace Drupal\aeon\Plugin\Preprocess;

use Drupal\aeon\Utility\Variables;
use Drupal\Core\Template\Attribute;

/**
 * Pre-processes variables for the "menu" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @AeonPreprocess("menu")
 */
class Menu extends PreprocessBase {

  /**
   * {@inheritdoc}
   */
  public function preprocessVariables(Variables $variables) {
    $menu_name = $variables['menu_name'];

    if ($menu_name != 'admin') {
      // Preprocess each item of a menu.
      $this->alterChildren($variables['items']);
    }
  }

  /**
   * Alter menu item children.
   */
  protected function alterChildren(&$items) {
    foreach ($items as &$item) {
      $item = $this->alterChild($item);
      if (!empty($item['below'])) {
        $item['below'] = $this->alterChildren($item['below']);
      }
    }
    return $items;
  }

  /**
   * Alter individual menu item children.
   */
  protected function alterChild($item) {
    $options = $item['url']->getOptions();
    $options['attributes']['class'][] = 'item';
    $item['url']->setOptions($options);
    $item['link_attributes'] = new Attribute($options['attributes']);
    return $item;
  }

}
