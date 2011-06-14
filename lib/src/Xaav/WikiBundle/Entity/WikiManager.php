<?php

namespace Xaav\WikiBundle\Entity;

use Glip\GitTree;
use Glip\GitCommit;
use Glip\GitBranch;
use Glip\Git;

class WikiManager
{

    protected $page_repository;
    protected $revision_repository;

    /**
     * @var Git
     */
    protected $git_repository;

    public function __construct(Git $git_repository)
    {
        $this->git_repository = $git_repository;
    }

    public function getPageRepository()
    {
        if($this->page_repository){

            return $this->page_repository;
        }
        else {

            $repository = $this->page_repository = new PageRepository($this->git_repository);
            $repository->_manager = $this;

            return $this->page_repository = $repository;
        }
    }

    public function getRevisionRepository()
    {
        if($this->revision_repository) {

            return $this->revision_repository;
        }
        else {

            $repository = new RevisionRepository($this->git_repository);
            $repository->_manager = $this;

            return $this->revision_repository = $repository;
        }
    }

    /**
     * @return GitBranch
     */
    protected function getMaster()
    {
        return $this->git_repository['master'];
    }

    /**
     * @return GitCommit
     */
    protected function getTip()
    {
        return $this->getMaster()->getTip();
    }
}
