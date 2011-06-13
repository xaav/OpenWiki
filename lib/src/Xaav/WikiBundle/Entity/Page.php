<?php

namespace Xaav\WikiBundle\Entity;

class Page
{
    protected $title;
    protected $content;

    protected $revision;

    public function __construct(Revison $revision)
    {
        $this->revision = $revision;
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
}