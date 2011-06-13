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
        return new Page($this->commit->tree[Page::getPathFromTitle($title)]);
    }

    public function addPage(Page $page)
    {
        $this->commit->tree[$page->getPath()] = $page->getBlob();
    }

}