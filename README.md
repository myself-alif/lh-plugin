# Lh Solutions Plugin #

Requires at least: 5.2

Requires PHP:      7.4

Author:            LEMON HIVE

Author URI:        https://lemonhive.com/

Text Domain:       wp-lh-solutions-plugin

License:           Proprietary

## Description ##

This is an template to start develop any Wordpress plugin made by LEMON HIVE.

Handle all custom necessary hooks, filters, and changes needed by the project.

## Directory structure ##

```
|
|-- assets
|  |-- plugin
|     |-- css
|     |-- js
|-- src
|  |-- Plugins_Integrations
|  |-- Admin_Interfaces.php
|  |-- Ajax.php
|  |-- autoload.php
|  |-- constants.php
|  |-- Install_Uninstall.php
|  |-- Setup.php
|-- composer.json
|-- README.md
|-- wp-lh-solutions-plugin.php
```

* assets
  * In this folder should be stored all the CSS and JS assets used by the plugin.
  * assets/plugin
    * In this folder should be stored any custom CSS and JS created for this plugin.
* src
  * In this folder are all the classes of the plugin.
  * src/Plugins_Integrations
    * In this folder should be put all the classes that are created to handle the way this plugin interacts with other plugins.
  * src/Admin_Interfaces.php
    * The class where admin interfaces are registered.
    * Please change the root namespace to be the same as in autoload function.
  * src/Ajax.php
    * The class where the AJAX callbacks are registerd.
    * Please change the root namespace to be the same as in autoload function.
  * src/autoload.php
    * The registration of the autoload function for this plugin.
    * Please change the root namespace to be unique to this plugin.
  * src/constants.php
    * The file were all the plugins constants are defined.
  * src/Install_Uninstall.php
    * The class that contains the install, disable, and uninstall function hooks.
  * src/Setup.php
    * The main class of the plugin where is initilised the plugin and all the features it has.
    * Please change the root namespace to be the same as in autoload function.
* composer.json
  * Composer configuration.
* README.md
  * Description of the plugin and its configuration.
* wp-lh-solutions-plugin.php
  * Main plugin file.
  * Rename this file.

## Configuration ##

### Replace template name ###

Search and replace the following strings from plugin's files with the proper ones:
* Lh Solutions Plugin
* Lh Solutions
* LH
* LH
* wp-lh-solutions-plugin
* lh-theme
* Lh Solutions Theme settings
* lemonhive.com

Rename the main plugin file from wp-lh-solutions-plugin.php