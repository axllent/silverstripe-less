<?php
Requirements::set_backend(new LessCompiler());

/* Add default ThemeDir variable */
LessCompiler::addVariable('ThemeDir', '"' . Director::baseURL() . SSViewer::get_theme_folder() . '"');

/* Set default cache directory */
LessCompiler::setCacheDir(Director::baseFolder() . '/' . SSViewer::get_theme_folder() . '/.tmp/less');

/* Set default cache method */
LessCompiler::setCacheMethod('serialize');
