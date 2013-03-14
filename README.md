#LESS CSS Module for SilverStripe

Simple wrapper for [lessphp](http://leafo.net/lessphp/) to integrate [LESS](http://lesscss.org/) in SilverStripe.

## Features
* Uses lessphp http://leafo.net/lessphp/
* Based on lesscss from https://github.com/tardinha/silverstripe-lesscss
* Includes flushing option (?flush=1) to regenerate CSS stylesheets
(ie. force undetected less changes with @import)
* Check all required *.css files for a *.less equevelant, so works transparently.

## Requirements

 * SilverStripe 2 or 3
 * Webserver read & write permissions to the directories containing
 the *.less files to write compiled css files

## Usage

In your Template.ss you can refer to your less files either by name (eg: stylesheet.less) or
stylesheet.css (the parser will check to see if there is a less file for all css files).

<pre>
	&lt;% require css(themes/mytheme/css/stylesheet.less) %&gt;
</pre>

or in your Page Controller you could:

### Method 1
<pre>
class Page_Controller extends ContentController {

	public function init() {
		parent::init();
		if ( Director::isDev() ){
			Requirements::css($this-&gt;ThemeDir() . '/css/stylesheet1.less');
			Requirements::css($this-&gt;ThemeDir() . '/css/stylesheet2.less');
			Requirements::css($this-&gt;ThemeDir() . '/css/stylesheet3.less');
		} else {
			/* combined.less simply includes a merged list of the above stylesheets
			 * in the same order as above:
			 * @import "stylesheet1";
			 * @import "stylesheet2";
			 * @import "stylesheet3";
			*/
			Requirements::css($this-&gt;ThemeDir() . '/css/combined.less');
		}
	}

}
</pre>

### Method 2
<pre>
class Page_Controller extends ContentController {

	public function init() {
		parent::init();
		/* The parser will find css/stylesheet[1-3].less files are parse those before combining */
		$css[] = $this-&gt;ThemeDir() . '/css/stylesheet1.css';
		$css[] = $this-&gt;ThemeDir() . '/css/stylesheet2.css';
		$css[] = $this-&gt;ThemeDir() . '/css/stylesheet3.css';
		Requirements::combine_files('combined.css', $css);
		Requirements::process_combined_files();
	}

}
</pre>
