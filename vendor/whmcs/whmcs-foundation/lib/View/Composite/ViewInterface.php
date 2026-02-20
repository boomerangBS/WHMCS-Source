<?php

namespace WHMCS\View\Composite;

interface ViewInterface
{
    public function init();
    public function make();
    public function getTemplate();
    public function withBaseData($data);
    public function with($data);
    public function data() : \Illuminate\Support\Collection;
    public function render();
}

?>