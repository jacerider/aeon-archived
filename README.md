# Aeon

Aeon is a base theme for Drupal. Aeon has Sass, Gulp, BrowserSync along with an option to use Foundation or eXo.

## Quick Start for Drupal 8 or 9

* Download and enable `aeon`:

**Drupal 8**
```bash
drush dl aeon
drush en aeon -y
drush config-set system.theme default aeon -y
```
**Drupal 9**
```bash
drush dl aeon
drush theme:enable aeon -y
drush config-set system.theme default aeon -y
```

* Create a sub-theme:
    * **NOTE**: The default sub-theme will be based on Foundation. To include eXo and remove Foundation, create a sub-theme with the Valerian kit using: `drush --include=web/themes/aeon aeon "SUBTHEME NAME" --kit=valerian`
    * **NOTE**: If you intend on using the Valerian Kit and you cloned the repo, checkout the proper branch otherwise it will fail to load.

```bash
drush cc drush
drush aeon "SUBTHEME_NAME"
```

* Set default theme:

**Drupal 8**
```bash
drush en SUBTHEME_NAME
drush config-set system.theme default SUBTHEME_NAME -y
```
**Drupal 9**
```bash
drush theme:enable SUBTHEME_NAME -y
drush config-set system.theme default SUBTHEME_NAME -y
```

* Install required modules:

**Default**
```bash
cd /path/to/subtheme/dev
npm install
```

**Valerian Kit**
```bash
cd /path/to/subtheme/
npm install
```

* Update proxy in `/path/to/subtheme/dev/config/config.local.json`
* Run `gulp` to watch for Sass changes
