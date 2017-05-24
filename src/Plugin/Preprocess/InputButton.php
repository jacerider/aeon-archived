<?php

namespace Drupal\aeon\Plugin\Preprocess;

use Drupal\aeon\Utility\Element;
use Drupal\aeon\Utility\Variables;

/**
 * Pre-processes variables for the "input__button" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @AeonPreprocess("input__button")
 */
class InputButton extends Input implements PreprocessInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocessElement(Element $element, Variables $variables) {
    $element->setIcon($element->getProperty('icon'), $element->getProperty('icon_only'), $element->getProperty('icon_position'));
    $variables['label'] = $element->getProperty('value');
    if ($element->getProperty('split')) {
      $variables->map([$variables::SPLIT_BUTTON]);
    }
    parent::preprocessElement($element, $variables);
  }

}
