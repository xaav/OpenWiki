<?php

namespace Xaav\WikiBundle\Entity;

abstract class Repository
{
    /**
     * @return GitCommit
     */
    public function getTip()
    {
        return $this->git_repository->getTip()->getObject();
    }
}