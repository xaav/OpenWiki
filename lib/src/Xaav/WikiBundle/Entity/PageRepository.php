<?php

namespace Xaav\WikiBundle\Entity;

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

    public function findLatestByTitle($title)
    {

    }

    public function persist(Page $page)
    {

    }
}