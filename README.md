LESS CSS Module for SilverStripe
================================

A wrapper for [lessphp](http://leafo.net/lessphp/) to integrate [LESS](http://lesscss.org/) into SilverStripe.

## Features

* Uses lessphp http://leafo.net/lessphp/
* Based on lesscss from https://github.com/tardinha/silverstripe-lesscss
* Includes flushing option (?flush=1) to regenerate CSS stylesheets
(ie. force undetected less changes with @import)
* Check all required *.css files for a *.less equivalent, so works transparently.
* Allows custom global variables to be passed through to less compiling

## Requirements

* SilverStripe 2 or 3
* Webserver read & write permissions to the directories containing
the *.less files to write compiled css files

## Usage

In your `Template.ss` you can refer to your less files either by its "LESS name" (eg:`stylesheet.less`) or
"CSS name" (eg:`stylesheet.css`) - the parser will check to see if there is a less file for all css files included.

In your controller you can:

### Method 1

```php
class Page_Controller extends ContentController {

	public function init() {
		parent::init();
		if ( Director::isDev() ){
			Requirements::css($this->ThemeDir() . '/css/stylesheet1.less');
			Requirements::css($this->ThemeDir() . '/css/stylesheet2.less');
			Requirements::css($this->ThemeDir() . '/css/stylesheet3.less');
		} else {
			/* combined.less simply includes a merged list of the above stylesheets
			 * in the same order as above:
			 * @import "stylesheet1";
			 * @import "stylesheet2";
			 * @import "stylesheet3";
			*/
			Requirements::css($this->ThemeDir() . '/css/combined.less');
		}
	}

}
```

### Method 2

```php
class Page_Controller extends ContentController {

	public function init() {
		parent::init();
		/* The parser will find css/stylesheet[1-3].less files are compile those */
		$css[] = $this->ThemeDir() . '/css/stylesheet1.css';
		$css[] = $this->ThemeDir() . '/css/stylesheet2.css';
		$css[] = $this->ThemeDir() . '/css/stylesheet3.css';
		Requirements::combine_files('combined.css', $css);
		Requirements::process_combined_files();
	}

}
```

### Method 3

or you can call it directly from your template file:

```php
    <% require css(themes/mytheme/css/stylesheet.less) %>
```


## Custom global variables

By default silverstripe-lesscss includes one custom global variable `ThemeDir` which you are
able to use in your `less` files:

```css
div {
    background: url('@{ThemeDir}/images/icon-menu.png') no-repeat top center;
}
```
`@{ThemeDir}` will get substituted with "`Director::baseURL() . SSViewer::get_theme_folder()`".

You can add optionally your own global variables to your `mysite/_config.php`:

```php
LessCompiler::addVariable("StandardFont", "Arial, helvetica, sans-serif");
LessCompiler::addVariable("BaseURL" => "'http://example.com/'");
// note the double-quote, see below...
```

**Note**: Be aware that the value of the variable is a string containing a CSS value.
So if you want to pass a LESS string in, you're going to need two sets of quotes.
One for PHP and one for LESS. If you get the error "**lessphp fatal error: failed to
parse passed in variable**" then you probably need to add extra quoted to your value.
