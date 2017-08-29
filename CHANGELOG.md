# Changelog

Notable changes to this project will be documented in this file.

## [2.0.3]
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
