<?php

namespace Yaig\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Yaig\MediaBundle\Entity\Media as Media;
use Yaig\MediaBundle\Form\Type\MediaFormType as MediaFormType;
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
     * @Route("/media/delete/{id}", name="_media_delete")
     */
    public function deleteAction($id = 0)
    {
      if($id > 0 and $media = $this->getDoctrine()->getRepository('YaigMediaBundle:Media')->find($id))
      {
        if($media->getUserId() == $this->get('security.context')->getToken()->getUser()->getId())
        {
         $em = $this->getDoctrine()->getEntityManager();
         $em->remove($media);
         $em->flush();
        }
      }
     // $this->redirect($this->generateUrl('_homepage'));
      $url = $this->getRequest()->headers->get('referer');
      $url = $url ? $url : $this->generateUrl('_homepage');
      return new RedirectResponse($url);
    }
    
    /**
     * @Route("/media/new", name="_media_new", defaults={"album_id" = 0})
     * @Route("/media/new/{album_id}", name="_media_new_for_album")
     * @Template()
     */
    public function newAction($album_id = 0)
    {
      $media = new Media();
      $media->setName('ok');
      $media->setUser($this->get('security.context')->getToken()->getUser());
       
      if($album_id > 0)
      {
        $album = $this->getDoctrine()->getRepository('YaigAlbumBundle:Album')->find($album_id);
        $media->addAlbum($album);
      }
      
      $form = $this->createForm(new MediaFormType(), $media);
      if ($this->getRequest()->getMethod() === 'POST') {
          $form->bindRequest($this->getRequest());
          if ($form->isValid()) {
              $em = $this->getDoctrine()->getEntityManager();

              $media->upload();              
              
              $em->persist($media);
              $em->flush();

              $this->redirect($this->generateUrl('_homepage'));
          }
      }
       
      return array('form' => $form->createView());
    }
}
