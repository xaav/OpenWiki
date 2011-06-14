<?php

namespace Xaav\WikiBundle\Entity;

use Xaav\WikiBundle\Exception\NotImplementedException;
use Glip\Git;

class PageRepository
{
    /**
     * @var Git
     */
    protected $git_repository;

    public function __construct(Git $git_repository)
    {
        $this->git_repository = $git_repository;
    }

    /**
     * Find all pages that have the specified title.
     */
    public function findPagesByTitle($title)
    {
        throw new NotImplementedException();
    }
}