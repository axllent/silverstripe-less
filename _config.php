<?php
use Axllent\Less\LessCompiler;
use SilverStripe\View\Requirements;

$backend = LessCompiler::create();
Requirements::set_backend($backend);
