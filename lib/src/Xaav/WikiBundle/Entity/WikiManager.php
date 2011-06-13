<?php

namespace Xaav\WikiBundle\Entity;

use Glip\GitTree;
use Glip\GitCommit;
use Glip\GitBranch;
use Glip\Git;

class WikiManager
{

    protected $page_repository;
    protected $version_repository;

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

            return $this->page_repository = new PageRepository($this->git_repository);
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
