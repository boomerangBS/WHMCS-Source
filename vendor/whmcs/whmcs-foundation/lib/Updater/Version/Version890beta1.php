<?php

namespace WHMCS\Updater\Version;

class Version890beta1 extends IncrementalVersion
{
    protected $updateActions = [];
    public function __construct(\WHMCS\Version\SemanticVersion $version)
    {
        parent::__construct($version);
        $this->filesToRemove[] = ROOTDIR . "/.gitlab-ci.yml";
    }
}

?>