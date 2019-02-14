# unb_lib_theme
## Drupal 8 Theme Based on Bootstrap.

UNB Libraries Theme.

## License
- unb_lib_theme is licensed under the MIT License:
  - http://opensource.org/licenses/mit-license.html
- Attribution is not required, but much appreciated:
  - `unb_lib_theme by UNB Libraries`


## Notes
- This depends on Bootstrap Barrio theme:
  - https://www.drupal.org/project/bootstrap_barrio

- Add twbs/bootstrap 4.1.3 requirement to your project's composer.json to be able to compile Bootstrap Sass. 
  - https://github.com/twbs/bootstrap/tree/v4.1.3
  
- As of 8.x-4.17, the following patches should be installed in your build>composer.json
  - Fix 404: https://www.drupal.org/project/bootstrap_barrio/issues/2979684
  - Fix logo theme setting: https://www.drupal.org/project/bootstrap_barrio/issues/2996094