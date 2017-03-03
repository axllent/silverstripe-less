# Usage

SilverStripe-less is a plug-and-play module, meaning there is little you need to do.

Once you have [installed][Installation.md] the module, simply use `Requirements` as you normally would, except using the *.less names of your files.

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
        Requirements:css('themes/site/css/stylesheet.less');
    }
}
```

This will parse the less file (if needed), and write the resulting CSS file to `assets/_combinedfiles/themes-site-css-stylesheet.css`
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
        Requirements::combine_files('combined.css', [
            'themes/site/css/stylesheet.less',
            'themes/site/css/colours.less'
        ]);
        Requirements::process_combined_files();
    }
}
```

You can also include LESS stylesheets from within your templates:
```
<% require css(themes/site/css/stylesheet.less) %>
```

## Using site variables
Please refer to [Configuration](Configuration.md)
