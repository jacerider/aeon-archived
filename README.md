# Aeon

Aeon is a base theme for Drupal. Aeon has Sass, Gulp, BrowserSync along with an option to use Foudation or [eXo](#).

## Quick start for Drupal 8

1. Download and enable aeon: `drush dl aeon; drush en aeon -y; drush config-set system.theme default aeon -y`
2. Create a subtheme: `drush cc drush; drush aeon "SUBTHEME NAME"` 
    * **NOTE**: The default subtheme will rely on Foundation. To include eXo and remove Foundation dependencies create a subtheme with the Valerian kit using: `drush aeon "SUBTHEME NAME" --kit=valerian`
3. Set default theme: `drush en SUBTHEME_NAME -y; drush config-set system.theme default SUBTHEME_NAME -y`
4. Install required modules: cd /path/to/subtheme/dev; `npm install`
5. Update proxy in /path/to/subtheme/dev/config/config.local.json
6. Run `gulp` to watch for Sass changes.
