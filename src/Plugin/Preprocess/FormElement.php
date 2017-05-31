<?php

namespace Drupal\aeon\Plugin\Preprocess;

use Drupal\aeon\Utility\Element;
use Drupal\aeon\Utility\Variables;

/**
 * Pre-processes variables for the "form_element" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @AeonPreprocess("form_element")
 */
class FormElement extends PreprocessBase {

  /**
   * {@inheritdoc}
   */
  public function preprocessElement(Element $element, Variables $variables) {
    // Set has_error flag.
    $variables['has_error'] = $element->getProperty('has_error');

    if ($element->getProperty('autocomplete_route_name')) {
      $variables['is_autocomplete'] = TRUE;
    }

    $checkbox = $variables['is_checkbox'] = $element->isType('checkbox');
    $radio = $variables['is_radio'] = $element->isType('radio');

    $variables['content_tag'] = 'div';

    $variables->getAttributes($variables::CONTENT);
    $variables->addClass('field-input', $variables::CONTENT);

    if (($checkbox || $radio)) {
      $variables['content_tag'] = '';
    }

    // Place single checkboxes and radios in the label field.
    if (($checkbox || $radio)) {
      $label = Element::create($variables['label']);
      $children = &$label->getProperty('children', '');
      $children .= $variables['children'];
      unset($variables['children']);

      // Inform label if it is in checkbox/radio context.
      $label->setProperty('is_checkbox', $checkbox);
      $label->setProperty('is_radio', $radio);

      // Pass the label attributes to the label, if available.
      if ($element->hasProperty('label_attributes')) {
        $label->setAttributes($element->getProperty('label_attributes'));
      }
    }
  }

}
