<?php

namespace Xaav\WikiBundle\Entity;

use Glip\GitCommit;

class Revision
{
    protected $commit;

    public function __construct(GitCommit $commit)
    {
        $this->commit = $commit;
    }

    public function getPageByTitle($title)
    {

    }
}