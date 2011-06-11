<?php

namespace Xaav\WikiBundle\Entity;

class WikiManager
{
    protected $data_directory;
    protected $page_repository;
    protected $version_repository;

    public function __construct($data_directory)
    {
        $this->data_directory = $data_directory;
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
