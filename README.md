# Less.php module for SilverStripe

A wrapper for [less.php](https://github.com/Asenar/less.php) to integrate [LESS](http://lesscss.org/) into SilverStripe.

## Features

- Integrates a fork of [less.php](https://github.com/Asenar/less.php) seemessly into SilverStripe
- Includes flushing option (`?flush`) to regenerate CSS stylesheets (ie. force undetected less changes with @import)
- Writes processed *.css files into `assets/_css` and automatically modifies `Requirements` paths
- Allows custom global variables to be passed through to less compiling (yaml configuration)
- Automatic image & @import URL translation (eg: `url('../image.png')` will get rewritten as `url('/path/to/image.png')` depending on your website's root folder)
- Automatic compression of CSS files when in `Live` mode (may require an initial `?flush`)
- Adds any processed editor.less files to TinyMCE (must be included in your front-end template)

## Requirements

- SilverStripe 4

For SilverStripe 3, please refer to the [SilverStripe3 branch](https://github.com/axllent/silverstripe-less/tree/silverstripe3).

## Installation

```
composer require axllent/silverstripe-less
```

## Usage

You need refer to your less files by their full LESS filenames (eg:`stylesheet.less`).

## Example

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

The generated HTML will point automatically to the **processed** CSS file in `assets/_css`
rather than the original less file location, for example

```
<link rel="stylesheet" type="text/css"  href="/assets/_css/themes-site-css-stylesheet.css?m=123456789" />
```

## Further documentation

- [Usage.md](docs/en/Usage.md) for usage examples.
- [Configuration.md](docs/en/Configuration.md) for configuration options.
- View [Changelog](CHANGELOG.md)
