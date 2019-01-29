# Changelog

Notable changes to this project will be documented in this file.

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
- Generate CSS files in `assets/_combinedfiles` rather than *.less folder
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
