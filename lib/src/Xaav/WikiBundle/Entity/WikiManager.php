<?php

namespace Xaav\WikiBundle\Entity;

use Glip\GitTree;
use Glip\GitCommit;
use Glip\GitBranch;
use Glip\Git;

class WikiManager
{
    /**
     * @deprecated
     */
    protected $data_directory;
    protected $page_repository;
    protected $version_repository;

    /**
     * @var Git
     */
    protected $git_repository;

    public function __construct($data_directory, Git $git_repository)
    {
        $this->git_repository = $git_repository;
        $this->data_directory = $data_directory;
    }

    /**
     * @return GitBranch
     */
    public function getMaster()
    {
        return $this->git_repository['master'];
    }

    /**
     * @return GitCommit
     */
    public function getTip()
    {
        return $this->getMaster()->getTip();
    }

    /**
     * @return GitTree
     */
    public function getTree()
    {
        return $this->getTip()->tree;
    }

    public function getPageRepository()
    {
        if($this->page_repository){

            return $this->page_repository;
        }
        else {

            return $this->page_repository = new PageRepository($this->data_directory, $this);
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
