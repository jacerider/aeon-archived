<?php

namespace Drupal\aeon\Plugin\Alter;

use Drupal\aeon\Plugin\PluginBase;
use Drupal\aeon\Utility\Unicode;
use Drupal\aeon\Utility\Variables;
use Drupal\Core\Entity\EntityInterface;

/**
 * Implements hook_theme_suggestions_alter().
 *
 * @ingroup plugins_alter
 *
 * @AeonAlter("theme_suggestions")
 */
class ThemeSuggestions extends PluginBase implements AlterInterface {

  /**
   * {@inheritdoc}
   */
  public function alter(&$suggestions, &$context1 = NULL, &$hook = NULL) {
    $variables = Variables::create($context1);

    switch ($hook) {
      case 'page':
        $route_name = \Drupal::routeMatch()->getRouteName();
        switch ($route_name) {
          case 'user.login':
          case 'user.register':
          case 'user.pass':
          case 'user.reset.form':
            $suggestions[] = 'page__user__auth';
            break;
        }
        break;

      case 'links':
        if (Unicode::strpos($variables['theme_hook_original'], 'links__dropbutton') !== FALSE) {
          // Handle dropbutton "subtypes".
          // @see \Drupal\aeon\Plugin\Prerender\Dropbutton::preRenderElement()
          if ($suggestion = Unicode::substr($variables['theme_hook_original'], 17)) {
            $suggestions[] = 'aeon_dropdown' . $suggestion;
          }
          $suggestions[] = 'aeon_dropdown';
        }
        break;

      case 'fieldset':
      case 'details':
        if ($variables->element && $variables->element->getProperty('aeon_panel', TRUE)) {
          $suggestions[] = 'aeon_panel';
        }
        break;

      case 'input':
        if ($variables->element && $variables->element->isButton()) {
          if ($variables->element->getProperty('dropbutton')) {
            $suggestions[] = 'input__button__dropdown';
          }
          else {
            $suggestions[] = $variables->element->getProperty('split') ? 'input__button__split' : 'input__button';
          }
        }
        elseif (
          $variables->element &&
          !$variables->element->isType(['checkbox', 'hidden', 'radio'])
        ) {
          $suggestions[] = 'input__form_control';
        }
        break;

      // Add the "user" entity theme hook suggestions.
      // @see https://www.drupal.org/node/2828634
      // @see https://www.drupal.org/node/2808481
      // @todo Remove/refactor once core issue is resolved.
      case 'user':
        $this->addEntitySuggestions($suggestions, $variables, 'user');
        break;
    }

  }

  /**
   * Adds "bundle" and "view mode" suggestions for an entity.
   *
   * This is a helper method because core's implementation of theme hook
   * suggestions on entities is inconsistent.
   *
   * @param array $suggestions
   *   The suggestions array.
   * @param \Drupal\aeon\Utility\Variables $variables
   *   The variables object.
   * @param string $entity_type
   *   Optional. A specific type of entity to look for.
   * @param string $prefix
   *   Optional. A prefix (like "entity") to use. It will automatically be
   *   appended with the "__" separator.
   *
   * @see https://www.drupal.org/node/2808481
   *
   * @todo Remove/refactor once core issue is resolved.
   */
  public function addEntitySuggestions(array &$suggestions, Variables $variables, $entity_type = 'entity', $prefix = '') {
    // Immediately return if there is no element.
    if (!$variables->element) {
      return;
    }

    // Extract the entity.
    if ($entity = $this->getEntity($variables, $entity_type)) {
      $entity_type_id = $entity->getEntityTypeId();
      // Only add the entity type identifier if there's a prefix.
      if (!empty($prefix)) {
        $prefix .= '__';
        $suggestions[] = $prefix . '__' . $entity_type_id;
      }

      // View mode.
      if ($view_mode = preg_replace('/[^A-Za-z0-9]+/', '_', $variables->element->getProperty('view_mode'))) {
        $suggestions[] = $prefix . $entity_type_id . '__' . $view_mode;

        // Bundle.
        if ($entity->getEntityType()->hasKey('bundle')) {
          $suggestions[] = $prefix . $entity_type_id . '__' . $entity->bundle();
          $suggestions[] = $prefix . $entity_type_id . '__' . $entity->bundle() . '__' . $view_mode;
        }
      }

      // Ensure a unique array.
      $suggestions = array_unique($suggestions);
    }
  }

  /**
   * Extracts the entity from the element(s) passed in the Variables object.
   *
   * @param \Drupal\aeon\Utility\Variables $variables
   *   The Variables object.
   * @param string $entity_type
   *   Optional. The entity type to attempt to retrieve.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   The extracted entity, NULL if entity could not be found.
   */
  public function getEntity(Variables $variables, $entity_type = 'entity') {
    // Immediately return if there is no element.
    if (!$variables->element) {
      return NULL;
    }

    // Attempt to retrieve the provided element type.
    $entity = $variables->element->getProperty($entity_type);

    // If the provided entity type doesn't exist, check to see if a generic
    // "entity" property was used instead.
    if ($entity_type !== 'entity' && (!$entity || !($entity instanceof EntityInterface))) {
      $entity = $variables->element->getProperty('entity');
    }

    // Only return the entity if it's the proper object.
    return $entity instanceof EntityInterface ? $entity : NULL;
  }

}
