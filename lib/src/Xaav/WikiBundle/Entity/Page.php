<?php

namespace Xaav\WikiBundle\Entity;

use Glip\GitBlob;
use Xaav\WikiBundle\Entity\Revision;

class Page
{
    protected $title;
    protected $content;
    protected $blob;

    /**
     * @var Revision
     */
    protected $revision;

    public function __construct(GitBlob $blob = null)
    {
        $this->blob = $blob;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }

    /**
     * Calculates the path based on the title
     */
    public function getPath()
    {
        return $this->title.'.markdown';
    }

    /**
     * @return GitBlob
     */
    public function getBlob()
    {
        return $this->blob;
    }

    public function setBlob(GitBlob $blob)
    {
        $this->blob = $blob;
    }

    public function setRevision(Revision $revision)
    {
        $this->revision = $revision;
    }

    public function getRevision()
    {
        return $this->revision;
    }
}