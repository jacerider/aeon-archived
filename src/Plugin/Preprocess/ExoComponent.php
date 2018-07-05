<?php

namespace Drupal\aeon\Plugin\Preprocess;

use Drupal\aeon\Utility\Variables;
use Drupal\Core\Url;

/**
 * Pre-processes variables for the "exo_component" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @AeonPreprocess("exo_component")
 */
class ExoComponent extends PreprocessBase {
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
    $exo_component = $this->variables['exo_component'];
    if (isset($exo_component->{$field_name}) && !$exo_component->{$field_name}->isEmpty() && $uri = $exo_component->{$field_name}->uri) {
      $this->variables['tag'] = 'a';
      $this->variables['attributes']['href'] = Url::fromUri($uri)->toString();
      $this->variables['attributes']['rel'] = 'bookmark';
    }
  }

}
