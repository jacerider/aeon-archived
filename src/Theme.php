<?php

namespace Drupal\aeon;

use Drupal\aeon\Plugin\ProviderManager;
use Drupal\aeon\Plugin\SettingManager;
use Drupal\aeon\Plugin\UpdateManager;
use Drupal\aeon\Utility\Crypt;
use Drupal\aeon\Utility\Storage;
use Drupal\aeon\Utility\StorageItem;
use Drupal\Core\Extension\Extension;
use Drupal\Core\Extension\ThemeHandlerInterface;
use Drupal\Core\Site\Settings;

/**
 * Defines a theme object.
 *
 * @ingroup utility
 */
class Theme {

  /**
   * Ignores the following directories during file scans of a theme.
   *
   * @see \Drupal\aeon\Theme::IGNORE_ASSETS
   * @see \Drupal\aeon\Theme::IGNORE_CORE
   * @see \Drupal\aeon\Theme::IGNORE_DOCS
   * @see \Drupal\aeon\Theme::IGNORE_DEV
   */
  const IGNORE_DEFAULT = -1;

  /**
   * Ignores the directories "assets", "css", "images" and "js".
   */
  const IGNORE_ASSETS = 0x1;

  /**
   * Ignores the directories "config", "lib" and "src".
   */
  const IGNORE_CORE = 0x2;

  /**
   * Ignores the directories "docs" and "documentation".
   */
  const IGNORE_DOCS = 0x4;

  /**
   * Ignores "bower_components", "grunt", "node_modules" and "starterkits".
   */
  const IGNORE_DEV = 0x8;

  /**
   * Ignores the directories "templates" and "theme".
   */
  const IGNORE_TEMPLATES = 0x16;

  /**
   * Flag indicating if the theme is Aeon based.
   *
   * @var bool
   */
  protected $aeon;

  /**
   * Flag indicating if the theme is in "development" mode.
   *
   * This property can only be set via `settings.local.php`:
   *
   * @var bool
   *
   * @code
   * $settings['theme.dev'] = TRUE;
   * @endcode
   */
  protected $dev;

  /**
   * The current theme info.
   *
   * @var array
   */
  protected $info;

  /**
   * The theme machine name.
   *
   * @var string
   */
  protected $name;

  /**
   * The current theme Extension object.
   *
   * @var \Drupal\Core\Extension\Extension
   */
  protected $theme;

  /**
   * An array of installed themes.
   *
   * @var array
   */
  protected $themes;

  /**
   * Theme handler object.
   *
   * @var \Drupal\Core\Extension\ThemeHandlerInterface
   */
  protected $themeHandler;

  /**
   * The update plugin manager.
   *
   * @var \Drupal\aeon\Plugin\UpdateManager
   */
  protected $updateManager;

  /**
   * Theme constructor.
   *
   * @param \Drupal\Core\Extension\Extension $theme
   *   A theme \Drupal\Core\Extension\Extension object.
   * @param \Drupal\Core\Extension\ThemeHandlerInterface $theme_handler
   *   The theme handler object.
   */
  public function __construct(Extension $theme, ThemeHandlerInterface $theme_handler) {
    // Determine if "development mode" is set.
    $this->dev = !!Settings::get('theme.dev');

    $this->name = $theme->getName();
    $this->theme = $theme;
    $this->themeHandler = $theme_handler;
    $this->themes = $this->themeHandler->listInfo();
    $this->info = isset($this->themes[$this->name]->info) ? $this->themes[$this->name]->info : [];
    $this->aeon = $this->subthemeOf('aeon');

    // Only install the theme if it's Aeon based and there are no schemas
    // currently set.
    if ($this->isAeon() && !$this->getSetting('schemas')) {
      try {
        $this->install();
      }
      catch (\Exception $e) {
        // Intentionally left blank.
        // @see https://www.drupal.org/node/2697075
      }
    }
  }

  /**
   * Serialization method.
   */
  public function __sleep() {
    // Only store the theme name.
    return ['name'];
  }

  /**
   * Unserialize method.
   */
  public function __wakeup() {
    $theme_handler = Aeon::getThemeHandler();
    $theme = $theme_handler->getTheme($this->name);
    $this->__construct($theme, $theme_handler);
  }

  /**
   * Returns the theme machine name.
   *
   * @return string
   *   Theme machine name.
   */
  public function __toString() {
    return $this->getName();
  }

  /**
   * Retrieves the theme's settings array appropriate for drupalSettings.
   *
   * @return array
   *   The theme settings for drupalSettings.
   */
  public function drupalSettings() {
    // Immediately return if theme is not Aeon based.
    if (!$this->isAeon()) {
      return [];
    }

    $cache = $this->getCache('drupalSettings');
    $drupal_settings = $cache->getAll();
    if (!$drupal_settings) {
      foreach ($this->getSettingPlugin() as $name => $setting) {
        if ($setting->drupalSettings()) {
          $drupal_settings[$name] = TRUE;
        }
      }
      $cache->setMultiple($drupal_settings);
    }
    return array_intersect_key($this->settings()->get(), $drupal_settings);
  }

  /**
   * Wrapper for the core file_scan_directory() function.
   *
   * Finds all files that match a given mask in the given directories and then
   * caches the results. A general site cache clear will force new scans to be
   * initiated for already cached directories.
   *
   * @param string $mask
   *   The preg_match() regular expression of the files to find.
   * @param string $subdir
   *   Sub-directory in the theme to start the scan, without trailing slash. If
   *   not set, the base path of the current theme will be used.
   * @param array $options
   *   Options to pass, see file_scan_directory() for addition options:
   *   - ignore_flags: (int|FALSE) A bitmask to indicate which directories (if
   *     any) should be skipped during the scan. Must also not contain a
   *     "nomask" property in $options. Value can be any of the following:
   *     - \Drupal\aeon::IGNORE_CORE
   *     - \Drupal\aeon::IGNORE_ASSETS
   *     - \Drupal\aeon::IGNORE_DOCS
   *     - \Drupal\aeon::IGNORE_DEV
   *     - \Drupal\aeon::IGNORE_THEME
   *     Pass FALSE to iterate over all directories in $dir.
   *
   * @return array
   *   An associative array (keyed on the chosen key) of objects with 'uri',
   *   'filename', and 'name' members corresponding to the matching files.
   *
   * @see file_scan_directory()
   */
  public function fileScan($mask, $subdir = NULL, array $options = []) {
    $path = $this->getPath();

    // Append addition sub-directories to the path if they were provided.
    if (isset($subdir)) {
      $path .= '/' . $subdir;
    }

    // Default ignore flags.
    $options += [
      'ignore_flags' => self::IGNORE_DEFAULT,
    ];
    $flags = $options['ignore_flags'];
    if ($flags === self::IGNORE_DEFAULT) {
      $flags = self::IGNORE_CORE | self::IGNORE_ASSETS | self::IGNORE_DOCS | self::IGNORE_DEV;
    }

    // Save effort by skipping directories that are flagged.
    if (!isset($options['nomask']) && $flags) {
      $ignore_directories = [];
      if ($flags & self::IGNORE_ASSETS) {
        $ignore_directories += ['assets', 'css', 'images', 'js'];
      }
      if ($flags & self::IGNORE_CORE) {
        $ignore_directories += ['config', 'lib', 'src'];
      }
      if ($flags & self::IGNORE_DOCS) {
        $ignore_directories += ['docs', 'documentation'];
      }
      if ($flags & self::IGNORE_DEV) {
        $ignore_directories += ['bower_components', 'grunt', 'node_modules', 'starterkits'];
      }
      if ($flags & self::IGNORE_TEMPLATES) {
        $ignore_directories += ['templates', 'theme'];
      }
      if (!empty($ignore_directories)) {
        $options['nomask'] = '/^' . implode('|', $ignore_directories) . '$/';
      }
    }

    // Retrieve cache.
    $files = $this->getCache('files');

    // Generate a unique hash for all parameters passed as a change in any of
    // them could potentially return different results.
    $hash = Crypt::generateHash($mask, $path, $options);

    if (!$files->has($hash)) {
      $files->set($hash, file_scan_directory($path, $mask, $options));
    }
    return $files->get($hash, []);
  }

  /**
   * Retrieves the full base/sub-theme ancestry of a theme.
   *
   * @param bool $reverse
   *   Whether or not to return the array of themes in reverse order, where the
   *   active theme is the first entry.
   *
   * @return \Drupal\aeon\Theme[]
   *   An associative array of \Drupal\aeon objects (theme), keyed
   *   by machine name.
   */
  public function getAncestry($reverse = FALSE) {
    $ancestry = $this->themeHandler->getBaseThemes($this->themes, $this->getName());
    foreach (array_keys($ancestry) as $name) {
      $ancestry[$name] = Aeon::getTheme($name, $this->themeHandler);
    }
    $ancestry[$this->getName()] = $this;
    return $reverse ? array_reverse($ancestry) : $ancestry;
  }

  /**
   * Retrieves an individual item from a theme's cache in the database.
   *
   * @param string $name
   *   The name of the item to retrieve from the theme cache.
   * @param array $context
   *   Optional. An array of additional context to use for retrieving the
   *   cached storage.
   * @param mixed $default
   *   Optional. The default value to use if $name does not exist.
   *
   * @return mixed|\Drupal\aeon\Utility\StorageItem
   *   The cached value for $name.
   */
  public function getCache($name, array $context = [], $default = []) {
    static $cache = [];

    // Prepend the theme name as the first context item, followed by cache name.
    array_unshift($context, $name);
    array_unshift($context, $this->getName());

    // Join context together with ":" and use it as the name.
    $name = implode(':', $context);

    if (!isset($cache[$name])) {
      $storage = self::getStorage();
      $value = $storage->get($name);
      if (!isset($value)) {
        $value = is_array($default) ? new StorageItem($default, $storage) : $default;
        $storage->set($name, $value);
      }
      $cache[$name] = $value;
    }

    return $cache[$name];
  }

  /**
   * Retrieves the theme info.
   *
   * @param string $property
   *   A specific property entry from the theme's info array to return.
   *
   * @return array
   *   The entire theme info or a specific item if $property was passed.
   */
  public function getInfo($property = NULL) {
    if (isset($property)) {
      return isset($this->info[$property]) ? $this->info[$property] : NULL;
    }
    return $this->info;
  }

  /**
   * Returns the machine name of the theme.
   *
   * @return string
   *   The machine name of the theme.
   */
  public function getName() {
    return $this->theme->getName();
  }

  /**
   * Returns the relative path of the theme.
   *
   * @return string
   *   The relative path of the theme.
   */
  public function getPath() {
    return $this->theme->getPath();
  }

  /**
   * Retrieves pending updates for the theme.
   *
   * @return \Drupal\aeon\Plugin\Update\UpdateInterface[]
   *   An array of update plugin objects.
   */
  public function getPendingUpdates() {
    $pending = [];

    // Only continue if the theme is Aeon based.
    if ($this->isAeon()) {
      $current_theme = $this->getName();
      $schemas = $this->getSetting('schemas', []);
      foreach ($this->getAncestry() as $ancestor) {
        $ancestor_name = $ancestor->getName();
        if (!isset($schemas[$ancestor_name])) {
          $schemas[$ancestor_name] = \Drupal::CORE_MINIMUM_SCHEMA_VERSION;
          $this->setSetting('schemas', $schemas);
        }
        $pending_updates = $ancestor->getUpdateManager()->getPendingUpdates($current_theme === $ancestor_name);
        foreach ($pending_updates as $schema => $update) {
          if ((int) $schema > (int) $schemas[$ancestor_name]) {
            $pending[] = $update;
          }
        }
      }
    }

    return $pending;
  }

  /**
   * Retrieves the CDN provider.
   *
   * @param string $provider
   *   A CDN provider name. Defaults to the provider set in the theme settings.
   *
   * @return \Drupal\aeon\Plugin\Provider\ProviderInterface|false
   *   A provider instance or FALSE if there is no provider.
   */
  public function getProvider($provider = NULL) {
    // Only continue if the theme is Aeon based.
    if ($this->isAeon()) {
      $provider = $provider ?: $this->getSetting('cdn_provider');
      $provider_manager = new ProviderManager($this);
      if ($provider_manager->hasDefinition($provider)) {
        return $provider_manager->createInstance($provider, ['theme' => $this]);
      }
    }
    return FALSE;
  }

  /**
   * Retrieves all CDN providers.
   *
   * @return \Drupal\aeon\Plugin\Provider\ProviderInterface[]
   *   All provider instances.
   */
  public function getProviders() {
    $providers = [];

    // Only continue if the theme is Aeon based.
    if ($this->isAeon()) {
      $provider_manager = new ProviderManager($this);
      foreach (array_keys($provider_manager->getDefinitions()) as $provider) {
        if ($provider === 'none') {
          continue;
        }
        $providers[$provider] = $provider_manager->createInstance($provider, ['theme' => $this]);
      }
    }

    return $providers;
  }

  /**
   * Retrieves a theme setting.
   *
   * @param string $name
   *   The name of the setting to be retrieved.
   * @param mixed $default
   *   A default value to provide if the setting is not found or if the plugin
   *   does not have a "defaultValue" annotation key/value pair. Typically,
   *   you will likely never need to use this unless in rare circumstances
   *   where the setting plugin exists but needs a default value not able to
   *   be set by conventional means (e.g. empty array).
   *
   * @return mixed
   *   The value of the requested setting, NULL if the setting does not exist
   *   and no $default value was provided.
   *
   * @see theme_get_setting()
   */
  public function getSetting($name, $default = NULL) {
    $value = $this->settings()->get($name);
    return !isset($value) ? $default : $value;
  }

  /**
   * Retrieves a theme's setting plugin instance(s).
   *
   * @param string $name
   *   Optional. The name of a specific setting plugin instance to return.
   *
   * @return \Drupal\aeon\Plugin\Setting\SettingInterface|\Drupal\aeon\Plugin\Setting\SettingInterface[]|null
   *   If $name was provided, it will either return a specific setting plugin
   *   instance or NULL if not set. If $name was omitted it will return an array
   *   of setting plugin instances, keyed by their name.
   */
  public function getSettingPlugin($name = NULL) {
    $settings = [];

    // Only continue if the theme is Aeon based.
    if ($this->isAeon()) {
      $setting_manager = new SettingManager($this);
      foreach (array_keys($setting_manager->getDefinitions()) as $setting) {
        $settings[$setting] = $setting_manager->createInstance($setting);
      }
    }

    // Return a specific setting plugin.
    if (isset($name)) {
      return isset($settings[$name]) ? $settings[$name] : NULL;
    }

    // Return all setting plugins.
    return $settings;
  }

  /**
   * Retrieves the theme's setting plugin instances.
   *
   * @return \Drupal\aeon\Plugin\Setting\SettingInterface[]
   *   An associative array of setting objects, keyed by their name.
   *
   * @deprecated Will be removed in a future release. Use \Drupal\aeon\Theme::getSettingPlugin instead.
   */
  public function getSettingPlugins() {
    Aeon::deprecated();
    return $this->getSettingPlugin();
  }

  /**
   * Retrieves the theme's cache from the database.
   *
   * @return \Drupal\aeon\Utility\Storage
   *   The cache object.
   */
  public function getStorage() {
    static $cache = [];
    $theme = $this->getName();
    if (!isset($cache[$theme])) {
      $cache[$theme] = new Storage($theme);
    }
    return $cache[$theme];
  }

  /**
   * Retrieves the human-readable title of the theme.
   *
   * @return string
   *   The theme title or machine name as a fallback.
   */
  public function getTitle() {
    return $this->getInfo('name') ?: $this->getName();
  }

  /**
   * Retrieves the update plugin manager for the theme.
   *
   * @return \Drupal\aeon\Plugin\UpdateManager|false
   *   The Update plugin manager or FALSE if theme is not Aeon based.
   */
  public function getUpdateManager() {
    // Immediately return if theme is not Aeon based.
    if (!$this->isAeon()) {
      return FALSE;
    }

    if (!$this->updateManager) {
      $this->updateManager = new UpdateManager($this);
    }
    return $this->updateManager;
  }

  /**
   * Includes a file from the theme.
   *
   * @param string $file
   *   The file name, including the extension.
   * @param string $path
   *   The path to the file in the theme. Defaults to: "includes". Set to FALSE
   *   or and empty string if the file resides in the theme's root directory.
   *
   * @return bool
   *   TRUE if the file exists and is included successfully, FALSE otherwise.
   */
  public function includeOnce($file, $path = 'includes') {
    static $includes = [];
    $file = preg_replace('`^/?' . $this->getPath() . '/?`', '', $file);
    $file = strpos($file, '/') !== 0 ? $file = "/$file" : $file;
    $path = is_string($path) && !empty($path) && strpos($path, '/') !== 0 ? $path = "/$path" : '';
    $include = DRUPAL_ROOT . '/' . $this->getPath() . $path . $file;
    if (!isset($includes[$include])) {
      $includes[$include] = !!@include_once $include;
      if (!$includes[$include]) {
        drupal_set_message(t('Could not include file: @include', ['@include' => $include]), 'error');
      }
    }
    return $includes[$include];
  }

  /**
   * Installs a Aeon based theme.
   */
  protected function install() {
    // Immediately return if theme is not Aeon based.
    if (!$this->isAeon()) {
      return;
    }

    $schemas = [];
    foreach ($this->getAncestry() as $ancestor) {
      $schemas[$ancestor->getName()] = $ancestor->getUpdateManager()->getLatestSchema();
    }
    $this->setSetting('schemas', $schemas);
  }

  /**
   * Indicates whether the theme is aeon based.
   *
   * @return bool
   *   TRUE or FALSE
   */
  public function isAeon() {
    return $this->aeon;
  }

  /**
   * Indicates whether the theme is in "development mode".
   *
   * @return bool
   *   TRUE or FALSE
   *
   * @see \Drupal\aeon\Theme::dev
   */
  public function isDev() {
    return $this->dev;
  }

  /**
   * Removes a theme setting.
   *
   * @param string $name
   *   Name of the theme setting to remove.
   */
  public function removeSetting($name) {
    $this->settings()->clear($name)->save();
  }

  /**
   * Sets a value for a theme setting.
   *
   * @param string $name
   *   Name of the theme setting.
   * @param mixed $value
   *   Value to associate with the theme setting.
   */
  public function setSetting($name, $value) {
    $this->settings()->set($name, $value)->save();
  }

  /**
   * Retrieves the theme settings instance.
   *
   * @return \Drupal\aeon\ThemeSettings
   *   All settings.
   */
  public function settings() {
    static $themes = [];
    $name = $this->getName();
    if (!isset($themes[$name])) {
      $themes[$name] = new ThemeSettings($this, $this->themeHandler);
    }
    return $themes[$name];
  }

  /**
   * Determines whether or not a theme is a sub-theme of another.
   *
   * @param string|\Drupal\aeon\Theme $theme
   *   The name or theme Extension object to check.
   *
   * @return bool
   *   TRUE or FALSE
   */
  public function subthemeOf($theme) {
    return (string) $theme === $this->getName() || in_array($theme, array_keys(self::getAncestry()));
  }

}
