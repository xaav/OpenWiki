<?php

namespace Xaav\WikiBundle\Entity;

use Glip\GitBlob;
use Glip\GitCommit;

class Revision
{
    /**
     * @var GitCommit
     */
    protected $commit;

    public function __construct(GitCommit $commit)
    {
        $this->commit = $commit;
    }

    public function getPageByTitle($title)
    {
        $page = new Page();
        $page->setTitle($title);
        $page->setBlob($this->commit->tree[$page->getPath()]);

        return $page;
    }

    public function getCommit()
    {
        return $this->commit;
    }

    public function setCommit(GitCommit $commit)
    {
        $this->commit = $commit;
    }
}