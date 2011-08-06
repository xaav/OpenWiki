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
        return new Revision($this->_manager->getTip());
    }

    public function persist(Revision $revision)
    {
        $this->_manager->getMaster()->updateTipTo($revision->getCommit());
    }
}
