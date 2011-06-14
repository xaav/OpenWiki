<?php

namespace Xaav\WikiBundle\Entity;

use Glip\Git;

class RevisionRepository
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

    public function commitRevison(Revision $revision)
    {
        $this->getMaster()->updateTipTo($revision->getCommit());
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
