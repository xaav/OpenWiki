<?php

namespace Xaav\WikiBundle\Entity;

use Xaav\GitBundle\Git\GitBlob;

class Page extends Entity
{
    protected $title;

    /**
     * @var GitBlob
     */
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
        $this->blob->data['data'] = $content;
    }

    public function getContent()
    {
        return $this->blob->data;
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

    /**
     * Many pages to one revision.
     */
    public function getRevision()
    {
        return $this->revision;
    }
}