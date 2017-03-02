<?php

use Axllent\Less\LessCompiler;
// use SilverStripe\Control\Director;
use SilverStripe\View\Requirements;
// use SilverStripe\View\SSViewer;

$backend = LessCompiler::create();
Requirements::set_backend($backend);

/* Add default ThemeDir variable */
// Axllent\Less\LessCompiler::addVariable('ThemeDir', '"' . Director::baseURL() . SSViewer::get_theme_folder() . '"');

/* Set default cache directory */
// LessCompiler::setCacheDir(TEMP_FOLDER . '/less-cache');
