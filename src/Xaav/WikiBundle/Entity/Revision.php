<?php

namespace Xaav\WikiBundle\Entity;

use Xaav\GitBundle\Git\GitCommit;

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
        $page->setBlob($this->commit->getTree()->child($page->getPath()));

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