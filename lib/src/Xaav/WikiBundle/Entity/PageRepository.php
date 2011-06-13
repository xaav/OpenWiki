<?php

namespace Xaav\WikiBundle\Entity;

class PageRepository
{
    /**
     * @deprecated
     */
    protected $pages_directory;
    protected $manager;

    public function __construct($pages_directory, WikiManager $manager)
    {
        $this->manager = $manager;
        $this->pages_directory = $pages_directory;
    }

    public function getGitPageByFileName($filename)
    {
        $tree = $this->manager->getTree();

        return $tree[$filename];
    }

    public function findByTitle($title)
    {
        $page = new Page();
        $page->setTitle($title);

        $page->setContent(@file_get_contents($this->getFilenameByTitle($title)));

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