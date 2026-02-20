<?php

namespace WHMCS\Updater\Version;

class Version8121release1 extends IncrementalVersion
{
    public function __construct(\WHMCS\Version\SemanticVersion $version)
    {
        parent::__construct($version);
        $this->filesToRemove = [ROOTDIR . "/.github/"];
    }
}

?>