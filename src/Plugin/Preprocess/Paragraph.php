<?php

namespace Drupal\aeon\Plugin\Preprocess;

use Drupal\aeon\Utility\Variables;
use Drupal\Core\Url;

/**
 * Pre-processes variables for the "paragraph" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @AeonPreprocess("paragraph")
 */
class Paragraph extends PreprocessBase {
  use PreprocessEntityTrait;

  /**
   * Set the content tag.
   */
  protected function setContentOnly() {
    $this->variables['tag'] = NULL;
  }

  /**
   * Set element as link given a Link field.
   *
   * @var string
   *   The field name to fetch a URI from.
   */
  protected function setAsLink($field_name) {
    $paragraph = $this->variables['paragraph'];
    if (isset($paragraph->{$field_name}) && !$paragraph->{$field_name}->isEmpty() && $uri = $paragraph->{$field_name}->uri) {
      $this->variables['tag'] = 'a';
      $this->variables['attributes']['href'] = Url::fromUri($uri)->toString();
      $this->variables['attributes']['rel'] = 'bookmark';
    }
  }

}
