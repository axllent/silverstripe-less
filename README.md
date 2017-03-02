# Less.php module for SilverStripe
A wrapper for [less.php](http://lessphp.gpeasy.com/) to integrate [LESS](http://lesscss.org/) into SilverStripe.

## Features
- Integrates less.php [http://lessphp.gpeasy.com/](http://lessphp.gpeasy.com/) seemessly into SilverStripe
- Includes flushing option (?flush=1) to regenerate CSS stylesheets (ie. force undetected less changes with @import)
- Writes processed *.less files into `assets/_combinedfiles` and automatically modifies css paths
- Allows custom global variables to be passed through to less compiling
- Automatic image & @import URL translation (eg: `url('../image.png')` will get rewritten
as `url('/path/to/image.png')` depending on your website's root folder)
- Automatic compression of CSS files when in `Live` mode (may require an initial `?flush`)

## Requirements
- SilverStripe 4

For SilverStripe 3, please refer to the [SilverStripe3 branch](https://github.com/axllent/silverstripe-less/tree/silverstripe3).


## Installation
```
composer require axllent/silverstripe-less
```

## Usage
You need refer to your less files by its "LESS name" (eg:`stylesheet.less`).

### Method 1 (preferred)
You can refer to your files as `*.less` files.

```php
class PageController extends ContentController
{

    public function init()
    {
        parent::init();
        $css[] = 'themes/site/css/stylesheet1.less';
        $css[] = 'themes/site/css/stylesheet2.less';
        $css[] = 'themes/site/css/stylesheet3.less';
        Requirements::combine_files('combined.css', $css);
        Requirements::process_combined_files();
    }

}
```

### Method 2

You can import less files directly from your template file:

```php
    <% require css(themes/site/css/stylesheet.less) %>
```

In both examples, the generated HTML will point automatically to the **processed** CSS file in `assets/_combinedfiles` rather than the original less file, for example
```
<link rel="stylesheet" type="text/css"  href="/assets/_combinedfiles/themes-site-css-stylesheet.css?m=1488490838" />
```


## Custom global variables
If you wish to add custom variables you can simply add something like this to your `config.yml` file:

```
Axllent\Less\LessCompiler:
  variables:
    'HeaderFont': '"Arial, sans-serif"' # note the quotes, see below!
    'HeaderFontSize': '18px'
```

And then in your `*.less` files you can use those variables:

```
header h1 {
    font-family: @HeaderFont;
    font-size: @HeaderFontSize;
}
```

**Note**: Remember quote your yml variable values if your CSS requires a quoted string (see `HeaderFont` example above).
