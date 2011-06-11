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
use Knplabs\Bundle\MarkdownBundle\Parser\MarkdownParser;

class DefaultController extends Controller
{
    public function viewAction($title = 'index')
    {
        $page = $this->get('pagemanager')->findByTitle($title);

        $page->setContent(strip_tags($page->getContent()));
        $page->setContent($this->getParser()->transform($page->getContent()));

        $response = $this->render('XaavWikiBundle::view.html.twig', array(
            'page' => $page,
            'title' => sprintf('Viewing %s', $title),
        ));

        $response->setPublic();
        $response->setSharedMaxAge(600);

        return $response;
    }

    public function editAction($title = 'index')
    {
        $page = $this->get('pagemanager')->findByTitle($title);
        $form = $this->createForm(new PageType(), $page);

        if ($this->get('request')->getMethod() == 'POST') {
            $form->bindRequest($this->get('request'));

            if($form->isValid()) {

                $this->get('pagemanager')->persist($page);

                $this->setFlash('Page Saved', 'success');

                return $this->redirect($this->generateUrl('wiki_view', array(
                    'title' => $title,
                )));
            }
        }

        return $this->render('XaavWikiBundle::edit.html.twig', array(
            'title' => sprintf('Editing %s', $title),
            'form' => $form->createView(),
        ));
    }

    protected function setFlash($message, $type)
    {
        $this->container->get('session')->setFlash($message, $type);
    }

    /**
     * @return MarkdownParser
     */
    protected function getParser()
    {
        return $this->container->get('markdown.parser');
    }
}
