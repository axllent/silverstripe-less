# Less.php module for Silverstripe

A wrapper for [less.php](https://github.com/wikimedia/less.php) to integrate [LESS](http://lesscss.org/) into Silverstripe.


## Features

- Integrates a fork of [less.php](hhttps://github.com/wikimedia/less.php) seamlessly into Silverstripe
- Includes flushing option (`?flush`) to regenerate CSS stylesheets (ie. force undetected less changes with @import)
- Writes processed *.css files into `assets/_css` and automatically modifies `Requirements` paths
- Allows custom global variables to be passed through to less compiling (yaml configuration)
- Automatic compression of CSS files when in `live` mode (may require an initial `?flush`)
- Adds any processed editor.less files to TinyMCE (must be included in your front-end template)


## Requirements

- Silverstripe ^5


## Installation

```shell
composer require axllent/silverstripe-less
```

## Usage

You need refer to your less files by their full LESS filenames (eg:`stylesheet.less`).

Note: The `less.php` compiler transforms relative paths like `url('../images/logo.png')` into `url('/themes/site/images/logo.png')` based on the path provided as you included the files, meaning these won't work in Silverstripe due to the exposed directory structure via (`_resources/...`). The two simplest solutions are:

1. Use a variable in your less files to provide the path to your files (ie: do not use relative paths), or:
2. Include your files using "_resources" in the path to your less file, eg: `Requirements:css('_resources/themes/site/css/stylesheet.less');`


## Example

In your page controller:
```php
<?php
use SilverStripe\CMS\Controllers\ContentController;
use SilverStripe\View\Requirements;

class PageController extends ContentController
{
    public function init()
    {
        parent::init();
        Requirements:css('themes/site/css/stylesheet.less');
    }
}
```

Or via template

```html
<% require themedCSS("layout.less") %>
```

The generated HTML will point automatically to the **processed** CSS file in `assets/_css`
rather than the original less file location, for example

```
<link rel="stylesheet" type="text/css"  href="/assets/_css/themes-site-css-stylesheet.css?m=123456789" />
```

## Further documentation

- [Usage.md](docs/en/Usage.md) for usage examples.
- [Configuration.md](docs/en/Configuration.md) for configuration options.
- View [Changelog](CHANGELOG.md)
