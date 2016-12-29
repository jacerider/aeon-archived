# Aeon

Aeon is a base theme for is a base theme for Drupal. It has Foundation, Sass, Gulp, BrowserSync

## Quick start for Drupal 8

1. Download and enable aeon: drush dl aeon; drush en aeon -y; drush config-set system.theme default aeon -y
2. Create a subtheme: drush cc drush; drush aeon "SUBTHEME NAME"
3. Set default theme: drush en SUBTHEME_NAME -y; drush config-set system.theme default SUBTHEME_NAME -y
4. Install required modules: cd /path/to/subtheme; npm run setup
5. Update proxy in /path/to/subtheme/config.json
6. Watch: gulp
