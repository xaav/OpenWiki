<?php

namespace Xaav\WikiBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class PageType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('content', 'textarea');
    }

    public function getName()
    {
        return 'page';
    }
}