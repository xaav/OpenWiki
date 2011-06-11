<?php

namespace Xaav\WikiBundle\Entity;

class WikiManager
{
    protected $data_directory;

    public function __construct($data_directory)
    {
        $this->data_directory = $data_directory;
    }

    public function getPageRepository()
    {
        return new PageRepository($this->data_directory);
    }
}
