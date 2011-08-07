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

use Xaav\GitBundle\Git\GitRepository;
use Xaav\WikiBundle\Entity\WikiManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Xaav\WikiBundle\Form\PageType;
use Xaav\WikiBundle\Entity\Page;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Knplabs\Bundle\MarkdownBundle\Parser\MarkdownParser;

class DefaultController extends Controller
{
    public function getWikiManager()
    {
        return new WikiManager(new GitRepository(__DIR__.'/../../../../test.git'));
    }

    /**
     * @Route("/", name="wiki_index")
     * @Route("/{title}", name="wiki_view")
     */
    public function viewAction($title = 'index')
    {
        $page = $this
                    ->getWikiManager()
                    ->getRevisionRepository()
                    ->getLatest()
                    ->getPageByTitle($title);

        $this->get('logger')->debug($page->getContent());


        $page->setContent(strip_tags($page->getContent()));

        //$page->setContent($this->getParser()->transform($page->getContent()));

        $this->get('logger')->debug($page->getContent());

        $response = $this->render('XaavWikiBundle::view.html.twig', array(
            'page' => $page,
            'title' => sprintf('Viewing %s', $title),
        ));

        $response->setPublic();
        $response->setSharedMaxAge(600);

        return $response;
    }

    /**
     * @Route("/{title}/edit", name="wiki_edit")
     */
    public function editAction($title = 'index')
    {
        $page_repository = $this->get('wiki_manager')->getPageRepository();

        $page = $page_repository->findByTitle($title);
        $form = $this->createForm(new PageType(), $page);

        if ($this->get('request')->getMethod() == 'POST') {
            $form->bindRequest($this->get('request'));

            if($form->isValid()) {

                $page_repository->persist($page);

                $this->invalidate('wiki_view', array('title' => $title));

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
        return $this->get('markdown.parser');
    }

    protected function invalidate($route, $parameters = array())
    {
        $url = $this->generateUrl($route, $parameters, true);

        $context = stream_context_create(array('http'=>array('method'=>'PURGE')));
        $stream = fopen($url, 'r', false, $context);
        fclose($stream);
    }
}
