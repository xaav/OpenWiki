<?php

namespace Xaav\WikiBundle\Entity;

class VersionRepository
{
    protected $pages_directory;

    public function __construct($pages_directory)
    {
        $this->pages_directory = $pages_directory;
    }

    public function getPageVersions(Page $page)
    {
        //Return versions of page
    }
}
