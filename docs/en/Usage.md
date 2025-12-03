# Usage

Silverstripe-less is a plug-and-play module, meaning there is little you need to do.

Once you have [installed][Installation.md] the module, simply use `Requirements` as you normally would, except using the \*.less names of your files.

For instance if you have a `themes/site/css/stylesheet.less` file you wish to add, in your PageController you would have

```php
<?php
use SilverStripe\CMS\Controllers\ContentController;
use SilverStripe\View\Requirements;

class PageController extends ContentController
{
    public function init()
    {
        parent::init();
        Requirements::css('themes/site/css/stylesheet.less');
        // OR
        Requirements::themedCSS('css/stylesheet.less');
        // OR
        Requirements::themedCSS('stylesheet.less');
    }
}
```

The library supports `themedCSS()` file resolving mechanism. The following 3 lines are equivalent:

```php
Requirements::css('themes/site/css/stylesheet.less');
Requirements::themedCSS('css/stylesheet.less');
Requirements::themedCSS('stylesheet.less');
```

This will parse the less file (if needed), and write the resulting CSS file to `assets/_css/themes-site-css-stylesheet.css`
and automatically link the CSS in the templates to that file.

This also works if you are combining files:

```php
<?php
use SilverStripe\CMS\Controllers\ContentController;
use SilverStripe\View\Requirements;

class PageController extends ContentController
{
    public function init()
    {
        parent::init();
        Requirements::combine_files(
            'combined.css',
            [
                'themes/site/css/editor.less',
                'themes/site/css/stylesheet.less'
            ]
        );
    }
}
```

You can also include less stylesheets from within your templates:

```html
<% require css(themes/site/css/stylesheet.less) %>
<!-- OR -->
<% require themedCSS(css/stylesheet.less) %>
<!-- OR -->
<% require themedCSS(stylesheet.less) %>
```

## Using custom variables

Please refer to [Configuration](Configuration.md) documentation.
