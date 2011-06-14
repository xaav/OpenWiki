<?php

namespace Xaav\WikiBundle\Entity;

use Glip\GitBlob;

class Page
{
    protected $title;
    protected $content;
    protected $blob;

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
}