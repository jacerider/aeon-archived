<?php

namespace Drupal\aeon;

use Drupal\aeon\Plugin\AlterManager;
use Drupal\aeon\Plugin\FormManager;
use Drupal\aeon\Plugin\PreprocessManager;
use Drupal\aeon\Utility\Element;
use Drupal\aeon\Utility\Unicode;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Extension\ThemeHandlerInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * The primary class for the Drupal Aeon base theme.
 *
 * Provides many helper methods.
 *
 * @ingroup utility
 */
class Aeon {

  /**
   * Tag used to invalidate caches.
   *
   * @var string
   */
  const CACHE_TAG = 'theme_registry';

  /**
   * Append a callback.
   *
   * @var int
   */
  const CALLBACK_APPEND = 1;

  /**
   * Prepend a callback.
   *
   * @var int
   */
  const CALLBACK_PREPEND = 2;

  /**
   * Replace a callback or append it if not found.
   *
   * @var int
   */
  const CALLBACK_REPLACE_APPEND = 3;

  /**
   * Replace a callback or prepend it if not found.
   *
   * @var int
   */
  const CALLBACK_REPLACE_PREPEND = 4;

  /**
   * Returns the theme hook definition information.
   *
   * This base-theme's custom theme hook implementations. Never define "path"
   * as this is automatically detected and added.
   *
   * @see \Drupal\aeon\Plugin\Alter\ThemeRegistry::alter()
   * @see aeon_theme_registry_alter()
   * @see aeon_theme()
   * @see hook_theme()
   */
  public static function getThemeHooks() {
    $hooks = [];
    return $hooks;
  }

  /**
   * Check if this theme supports icons.
   *
   * @return bool
   *   Returns true if theme has icon support.
   */
  public static function hasIcons() {
    return \Drupal::service('module_handler')->moduleExists('micon');
  }

  /**
   * Iconizes a string using the iconify module.
   *
   * @param string $name
   *   The icon name, minus the "iconize-" prefix.
   * @param array $default
   *   (Optional) The default render array to use if $name is not available.
   *
   * @return array
   *   The render containing the icon defined by $name, $default value if
   *   icon does not exist or returns NULL if no icon could be rendered.
   */
  public static function iconize($text, $icon = NULL) {
    if (self::hasIcons()) {
      $text = micon($text);
    }
    return $text;
  }

  /**
   * Provides additional variables to be used in elements and templates.
   *
   * @return array
   *   An associative array containing key/default value pairs.
   */
  public static function extraVariables() {
    return [
      'tag' => 'div',
      'content_tag' => '',
      'title_tag' => '',

      // @see https://drupal.org/node/2035055
      'context' => [],

      // @see https://drupal.org/node/2219965
      'icon' => NULL,
      'icon_position' => 'before',
      'icon_only' => FALSE,
    ];
  }

  /**
   * Manages theme alter hooks as classes and allows sub-themes to sub-class.
   *
   * @param string $function
   *   The procedural function name of the alter (e.g. __FUNCTION__).
   * @param mixed $data
   *   The variable that was passed to the hook_TYPE_alter() implementation to
   *   be altered. The type of this variable depends on the value of the $type
   *   argument. For example, when altering a 'form', $data will be a structured
   *   array. When altering a 'profile', $data will be an object.
   * @param mixed $context1
   *   (optional) An additional variable that is passed by reference.
   * @param mixed $context2
   *   (optional) An additional variable that is passed by reference. If more
   *   context needs to be provided to implementations, then this should be an
   *   associative array as described above.
   */
  public static function alter($function, &$data, &$context1 = NULL, &$context2 = NULL) {
    static $theme;
    if (!isset($theme)) {
      $theme = self::getTheme();
    }

    // Immediately return if the active theme is not Aeon based.
    if (!$theme->isAeon()) {
      return;
    }

    // Extract the alter hook name.
    $hook = Unicode::extractHook($function, 'alter');

    // Handle form alters as a separate plugin.
    if (strpos($hook, 'form') === 0 && $context1 instanceof FormStateInterface) {
      $form_state = $context1;
      $form_id = $context2;

      // Due to a core bug that affects admin themes, we should not double
      // process the "system_theme_settings" form twice in the global
      // hook_form_alter() invocation.
      // @see https://drupal.org/node/943212
      if ($form_id === 'system_theme_settings') {
        return;
      }

      // Keep track of the form identifiers.
      $ids = [];

      // Get the build data.
      $build_info = $form_state->getBuildInfo();

      // Extract the base_form_id.
      $base_form_id = !empty($build_info['base_form_id']) ? $build_info['base_form_id'] : FALSE;
      if ($base_form_id) {
        $ids[] = $base_form_id;
      }

      // If there was no provided form identifier, extract it.
      if (!$form_id) {
        $form_id = !empty($build_info['form_id']) ? $build_info['form_id'] : Unicode::extractHook($function, 'alter', 'form');
      }
      if ($form_id) {
        $ids[] = $form_id;
      }

      // Retrieve a list of form definitions.
      $form_manager = new FormManager($theme);

      // Iterate over each form identifier and look for a possible plugin.
      foreach ($ids as $id) {
        /** @var \Drupal\aeon\Plugin\Form\FormInterface $form */
        if ($form_manager->hasDefinition($id) && ($form = $form_manager->createInstance($id, ['theme' => $theme]))) {
          $data['#submit'][] = [get_class($form), 'submitForm'];
          $data['#validate'][] = [get_class($form), 'validateForm'];
          $form->alterForm($data, $form_state, $form_id);
        }
      }
    }
    // Process hook alter normally.
    else {
      // Retrieve a list of alter definitions.
      $alter_manager = new AlterManager($theme);

      /** @var \Drupal\aeon\Plugin\Alter\AlterInterface $class */
      if ($alter_manager->hasDefinition($hook) && ($class = $alter_manager->createInstance($hook, ['theme' => $theme]))) {
        $class->alter($data, $context1, $context2);
      }
    }
  }

  /**
   * Initializes the active theme.
   */
  final public static function initialize() {
    static $initialized = FALSE;
    if (!$initialized) {
      // Initialize the active theme.
      $active_theme = self::getTheme();

      // Include deprecated functions.
      foreach ($active_theme->getAncestry() as $ancestor) {
        if ($ancestor->getSetting('include_deprecated')) {
          $files = $ancestor->fileScan('/^deprecated\.php$/');
          if ($file = reset($files)) {
            $ancestor->includeOnce($file->uri, FALSE);
          }
        }
      }

      $initialized = TRUE;
    }
  }

  /**
   * Preprocess theme hook variables.
   *
   * @param array $variables
   *   The variables array, passed by reference.
   * @param string $hook
   *   The name of the theme hook.
   * @param array $info
   *   The theme hook info.
   */
  public static function preprocess(array &$variables, $hook, array $info) {
    static $theme;
    if (!isset($theme)) {
      $theme = self::getTheme();
    }
    static $preprocess_manager;
    if (!isset($preprocess_manager)) {
      $preprocess_manager = new PreprocessManager($theme);
    }

    // Adds a global "is_front" variable back to all templates.
    // @see https://www.drupal.org/node/2829585
    if (!isset($variables['is_front'])) {
      $variables['is_front'] = static::isFront();
      if (static::hasIsFrontCacheContext()) {
        $variables['#cache']['contexts'][] = 'url.path.is_front';
      }
    }

    // Ensure that any default theme hook variables exist. Due to how theme
    // hook suggestion alters work, the variables provided are from the
    // original theme hook, not the suggestion.
    if (isset($info['variables'])) {
      $variables = NestedArray::mergeDeepArray([$info['variables'], $variables], TRUE);
    }

    // Add extra variables to all theme hooks.
    foreach (Aeon::extraVariables() as $key => $value) {
      if (!isset($variables[$key])) {
        $variables[$key] = $value;
      }
    }

    // Add active theme context.
    // @see https://www.drupal.org/node/2630870
    if (!isset($variables['theme'])) {
      $variables['theme'] = $theme->getInfo();
      $variables['theme']['dev'] = $theme->isDev();
      $variables['theme']['name'] = $theme->getName();
      $variables['theme']['path'] = $theme->getPath();
      $variables['theme']['title'] = $theme->getTitle();
      $variables['theme']['settings'] = $theme->settings()->get();
      $variables['theme']['query_string'] = \Drupal::getContainer()->get('state')->get('system.css_js_query_string') ?: '0';
    }

    // Invoke necessary preprocess plugin.
    if (isset($info['aeon preprocess'])) {
      if ($preprocess_manager->hasDefinition($info['aeon preprocess'])) {
        $class = $preprocess_manager->createInstance($info['aeon preprocess'], ['theme' => $theme]);
        /** @var \Drupal\aeon\Plugin\Preprocess\PreprocessInterface $class */
        $class->preprocess($variables, $hook, $info);
      }
    }
  }

  /**
   * Adds a callback to an array.
   *
   * @param array $callbacks
   *   An array of callbacks to add the callback to, passed by reference.
   * @param array|string $callback
   *   The callback to add.
   * @param array|string $replace
   *   If specified, the callback will instead replace the specified value
   *   instead of being appended to the $callbacks array.
   * @param int $action
   *   Flag that determines how to add the callback to the array.
   *
   * @return bool
   *   TRUE if the callback was added, FALSE if $replace was specified but its
   *   callback could be found in the list of callbacks.
   */
  public static function addCallback(array &$callbacks, $callback, $replace = NULL, $action = Aeon::CALLBACK_APPEND) {
    // Replace a callback.
    if ($replace) {
      // Iterate through the callbacks.
      foreach ($callbacks as $key => $value) {
        // Convert each callback and match the string values.
        if (Unicode::convertCallback($value) === Unicode::convertCallback($replace)) {
          $callbacks[$key] = $callback;
          return TRUE;
        }
      }
      // No match found and action shouldn't append or prepend.
      if ($action !== self::CALLBACK_REPLACE_APPEND || $action !== self::CALLBACK_REPLACE_PREPEND) {
        return FALSE;
      }
    }

    // Append or prepend the callback.
    switch ($action) {
      case self::CALLBACK_APPEND:
      case self::CALLBACK_REPLACE_APPEND:
        $callbacks[] = $callback;
        return TRUE;

      case self::CALLBACK_PREPEND:
      case self::CALLBACK_REPLACE_PREPEND:
        array_unshift($callbacks, $callback);
        return TRUE;

      default:
        return FALSE;
    }
  }

  /**
   * Determines if the "cache_context.url.path.is_front" service exists.
   *
   * @return bool
   *   TRUE or FALSE
   *
   * @see \Drupal\aeon\Aeon::isFront
   * @see \Drupal\aeon\Aeon::preprocess
   * @see https://www.drupal.org/node/2829588
   */
  public static function hasIsFrontCacheContext() {
    static $has_is_front_cache_context;
    if (!isset($has_is_front_cache_context)) {
      $has_is_front_cache_context = \Drupal::getContainer()->has('cache_context.url.path.is_front');
    }
    return $has_is_front_cache_context;
  }

  /**
   * Determines if the current path is the "front" page.
   *
   * *Note:* This method will not return `TRUE` if there is not a proper
   * "cache_context.url.path.is_front" service defined.
   *
   * *Note:* If using this method in preprocess/render array logic, the proper
   * #cache context must also be defined:
   *
   * ```php
   * $variables['#cache']['contexts'][] = 'url.path.is_front';
   * ```
   *
   * @return bool
   *   TRUE or FALSE
   *
   * @see \Drupal\aeon\Aeon::hasIsFrontCacheContext
   * @see \Drupal\aeon\Aeon::preprocess
   * @see https://www.drupal.org/node/2829588
   */
  public static function isFront() {
    static $is_front;
    if (!isset($is_front)) {
      try {
        $is_front = static::hasIsFrontCacheContext() ? \Drupal::service('path.matcher')->isFrontPage() : FALSE;
      }
      catch (\Exception $e) {
        $is_front = FALSE;
      }
    }
    return $is_front;
  }

  /**
   * Retrieves a theme instance of \Drupal\aeon.
   *
   * @param string $name
   *   The machine name of a theme. If omitted, the active theme will be used.
   * @param \Drupal\Core\Extension\ThemeHandlerInterface $theme_handler
   *   The theme handler object.
   *
   * @return \Drupal\aeon\Theme
   *   A theme object.
   */
  public static function getTheme($name = NULL, ThemeHandlerInterface $theme_handler = NULL) {
    // Immediately return if theme passed is already instantiated.
    if ($name instanceof Theme) {
      return $name;
    }

    static $themes = [];
    static $active_theme;
    if (!isset($active_theme)) {
      $active_theme = \Drupal::theme()->getActiveTheme()->getName();
    }
    if (!isset($name)) {
      $name = $active_theme;
    }

    if (!isset($theme_handler)) {
      $theme_handler = self::getThemeHandler();
    }

    if (!isset($themes[$name])) {
      $themes[$name] = new Theme($theme_handler->getTheme($name), $theme_handler);
    }

    return $themes[$name];
  }

  /**
   * Retrieves the theme handler instance.
   *
   * @return \Drupal\Core\Extension\ThemeHandlerInterface
   *   The theme handler instance.
   */
  public static function getThemeHandler() {
    static $theme_handler;
    if (!isset($theme_handler)) {
      $theme_handler = \Drupal::service('theme_handler');
    }
    return $theme_handler;
  }

  /**
   * Ensures a value is typecast to a string, rendering an array if necessary.
   *
   * @param string|array $value
   *   The value to typecast, passed by reference.
   *
   * @return string
   *   The typecast string value.
   */
  public static function toString(&$value) {
    return (string) (Element::isRenderArray($value) ? Element::create($value)->renderPlain() : $value);
  }

}
