<?php

/*
 * This file is part of the Symfony framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Xaav\WikiBundle\Controller;

use Xaav\WikiBundle\Form\PageType;
use Xaav\WikiBundle\Entity\Page;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function viewAction($page_name = 'index')
    {
        ob_start();

        @require_once PAGES_DIR . '/../index.php';

        return new Response(ob_get_clean());
    }

    public function editAction($page_name = '')
    {
        $page = new Page();
        $form = $this->createForm(new PageType(), $page);

        if ($this->get('request')->getMethod() == 'POST') {
            $form->bindRequest($this->get('request'));

            if($form->isValid()) {

                $this->get('pagemanager')->persist($page);

                $this->setFlash('Page Saved', 'success');

                return $this->redirect($this->generateUrl('wiki_view', array(
                    'page' => $page_name,
                )));
            }
        }

        return $this->render('XaavWikiBundle::edit.html.twig', array(
            'title' => sprintf('Editing %s', $page_name),
            'form' => $form->createView(),
        ));
    }

    protected function setFlash($message, $type)
    {
        $this->container->get('session')->setFlash($message, $type);
    }
}
