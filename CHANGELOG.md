# Changelog

Notable changes to this project will be documented in this file.

## [3.0.0]

- Drop support for Silverstripe 4
- Integration with axllent/silverstripe-minify



## [2.5.1]

- Bump `wikimedia/less.php` to ^4.1


## [2.5.0]

- Support for Silverstripe 5


## [2.4.0]

- Switch from abandoned `asenar/less.php` to `wikimedia/less.php`


## [2.3.1]

- New flush method to remove compiled files


## [2.3.0]

- Switch to using separate `$processed_folder` (default `_css`) due to upstream changes in `/dev/build` always emptying `_combinedfiles` causing issues with errorpage regeneration
- Remove (now) redundant ErrorPageController extension


## [2.2.0]

- Add third `$options` arg to `css()` for SS 4.5.0 compatibility
- Set requirement silverstripe/framework:^4.5


## [2.1.0]

- Ensure that error pages do not include combined assets


## [2.0.7]

- Switch to silverstripe-vendormodule


## [2.0.6]

- Use Injector to set Requirements_Backend


## [2.0.5]

- Init LessCompiler via Controller Extension rather than _config.php


## [2.0.4]

- Use static defaults
- Add any rendered editor.less to TinyMCE


## [2.0.3]

- Support changes in SilverStripe 4.0.0-beta1
- Use static defaults
- Add any rendered editor.less to TinyMCE


## [2.0.2]

- Fix bug mixing in CSS files


## [2.0.1]

- Fix bug when avoiding re-processing files


## [2.0.0]

- Support for SilverStripe 4 (namespacing)
- Generate CSS files in `assets/_css` rather than *.less folder
- Switch to YAML config
- Switch requirement to [asenar/less.php](https://github.com/Asenar/less.php) fork as
the original [less.php](http://lessphp.gpeasy.com/) isn't being maintained any more.
- Drop use of `*.css` files in `Requirements` - now you must use the `*.less` extensions (too much unneccessary overhead and checking)
- Completely rewrite `LessCompiler` to use system `AssetHandler` for generated CSS storage
- Add documentation & Changelog


## [1.1.0]

- Include lessphp as a requirement
- Modify required SilverStripe framework version (^3.0)


## [1.0.0]

- Adopt semantic versioning releases
