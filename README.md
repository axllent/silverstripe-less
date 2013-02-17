#LESS CSS Module for SilverStripe

Simple wrapper for [lessphp](http://leafo.net/lessphp/) to integrate [LESS](http://lesscss.org/) in SilverStripe.

## Features
* Uses lessphp http://leafo.net/lessphp/
* Based on lesscss from https://github.com/tardinha/silverstripe-lesscss
* Includes flushing option (?flush=1) to regenerate CSS stylesheets
(ie. force undetected less changes with @import)

## Requirements

 * SilverStripe 2 or 3
 * Webserver read & write permissions to the directories containing the *.less files to write cached css files

## Usage

In your Template.ss:

<pre>
	&lt;% require css(themes/mytheme/css/stylesheet.less) %&gt;
</pre>

or in your Page Controller you could:
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
