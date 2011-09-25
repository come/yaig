<?php

namespace Yaig\AlbumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Yaig\AlbumBundle\Entity\Album as Album;
use Yaig\AlbumBundle\Form\Type\AlbumFormType as AlbumFormType;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DefaultController extends Controller
{
    /**
     * @Route("/hello/{name}")
     * @Template()
     */
    public function indexAction($name)
    {
        return array('name' => $name);
    }
    
    /**
     * @Route("/album/new", name="_album_new")
     * @Template()
     */
    public function newAction()
    {
        $album = new Album();
        $album->setName('ok');
        
        $form = $this->createForm(new AlbumFormType(), $album);
        
        if ($this->getRequest()->getMethod() === 'POST') {
            $form->bindRequest($this->getRequest());
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($album);
                $em->flush();

               // $this->redirect($this->generateUrl('_homepage'));
                return new RedirectResponse($this->generateUrl('_album_show', array('id' => $album->getId())));
            }
        }
      
        return array('form' => $form->createView());
    }
    
    /**
     * @Route("/album/show/{id}", name="_album_show")
     * @Template()
     */
    public function showAction($id)
    {
        $album = $this->getDoctrine()->getRepository('YaigAlbumBundle:Album')->find($id);
        $medias = $album->getMedias();
        return array('album' => $album, 'medias' => $medias);
    }
    
    /**
     * @Route("/album/slideshow/{id}/{media_id}", name="_album_slideshow")
     * @Route("/album/slideshow/{id}")
     * @Template()
     */
    public function slideshowAction($id, $media_id = 0)
    {
        $album = $this->getDoctrine()->getRepository('YaigAlbumBundle:Album')->find($id);
        if($media_id == 0 or !$current_media = $this->getDoctrine()->getRepository('YaigMediaBundle:Media')->find($media_id))
        {
          $current_media = $album->getPicture();
        }
        $medias = $album->getMedias();
        $next = $medias->get($medias->indexOf($current_media)+1) ? $medias->get($medias->indexOf($current_media)+1)->getId() : $medias[0]->getId();
        $prev = $medias->get($medias->indexOf($current_media)-1) ? $medias->get($medias->indexOf($current_media)-1)->getId() : $medias[count($medias)-1]->getId();
        return array('album' => $album, 'current_media' => $current_media, 'medias' => $medias, 'next' => $next, 'prev' => $prev);
    }
}
