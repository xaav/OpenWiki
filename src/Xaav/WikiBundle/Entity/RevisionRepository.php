<?php

namespace Xaav\WikiBundle\Entity;

use Xaav\GitBundle\Git\GitRepository;

class RevisionRepository extends Repository
{
    protected $git_repository;

    public function __construct(GitRepository $git_repository)
    {
        $this->git_repository = $git_repository;
    }

    public function getLatest()
    {
        return new Revision($this->getTip());
    }
}
