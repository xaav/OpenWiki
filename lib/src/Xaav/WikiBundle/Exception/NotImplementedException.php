<?php

namespace Xaav\WikiBundle\Exception;

class NotImplementedException extends \RuntimeException
{
    function __construct()
    {
        parent::__construct('The feature you requested has not been implemented yet.');
    }
}