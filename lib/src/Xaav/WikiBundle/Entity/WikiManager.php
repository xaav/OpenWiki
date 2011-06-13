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
     * @deprecated
     */
    public function getMaster()
    {
        return $this->git_repository['master'];
    }

    /**
     * @return GitCommit
     * @deprecated
     */
    public function getTip()
    {
        return $this->getMaster()->getTip();
    }

    /**
     * @return GitTree
     * @deprecated
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

    public function getRevisionRepository()
    {
        if($this->revision_repository) {

            return $this->revision_repository;
        }
        else {

            return $this->revision_repository = new RevisionRepository($this->git_repository);
        }
    }
}
