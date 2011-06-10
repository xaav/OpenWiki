<?php

namespace Xaav\WikiBundle\Entity;

class PageManager
{
    protected $pages_directory;

    public function __construct($pages_directory)
    {
        $this->pages_directory = constant($pages_directory); //Hack
    }

    public function findByTitle($title)
    {
        $page = new Page();
        $page->setTitle($title);

        $page->setContent(file_get_contents($this->getFilenameByTitle($title)));

        return $page;
    }

    public function persist(Page $page)
    {
        $filename = $this->getFilenameByTitle($page->getTitle());
        $contents = $page->getContent();

        file_put_contents($filename, $contents);
    }

    protected function getFilenameByTitle($title)
    {
        return $this->pages_directory.'/'.$title.'.markdown';
    }
}