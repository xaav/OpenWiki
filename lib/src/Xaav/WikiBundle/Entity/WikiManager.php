<?php

namespace Xaav\WikiBundle\Entity;

use Glip\Git;

class WikiManager
{
    /**
     * @deprecated
     */
    protected $data_directory;
    protected $page_repository;
    protected $version_repository;
    protected $git_repository;

    public function __construct($data_directory, Git $git_repository)
    {
        $this->git_repository = $git_repository;
    }

    public function getPageRepository()
    {
        if($this->page_repository){

            return $this->page_repository;
        }
        else {

            return $this->page_repository = new PageRepository($this->data_directory);
        }
    }

    public function getVersionRepository()
    {
        if($this->version_repository) {

            return $this->version_repository;
        }
        else {

            return $this->version_repository = new VersionRepository($this->data_directory);
        }
    }
}
