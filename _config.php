<?php
Requirements::set_backend(new LessCompiler());

/* Add default ThemeDir variable */
LessCompiler::addVariable('ThemeDir', '"' . Director::baseURL() . SSViewer::get_theme_folder() . '"');