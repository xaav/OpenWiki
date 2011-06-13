<?php

namespace Xaav\WikiBundle\Entity;

use Glip\Git;

class RevisonRepository
{
    protected $git_repository;

    public function __construct(Git $git_repository)
    {
        $this->git_repository = $git_repository;
    }

    public function getLatest()
    {
        return new Revision($this->getTip());
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