<?php
namespace Axllent\Less\Extensions;

use Axllent\Less\LessCompiler;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Extension;
use SilverStripe\View\Requirements;

/**
 * Set backend for requirements to LessCompiler
 */
class LessController extends Extension
{
    public function onBeforeInit()
    {
        $backend = LessCompiler::create();
        Requirements::set_backend($backend);
    }
}
