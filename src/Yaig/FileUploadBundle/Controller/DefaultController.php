<?php

namespace Yaig\FileUploadBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Yaig\MediaBundle\Entity\Media as Media;


class DefaultController extends Controller
{
    /**
     * @Route("/media/upload", name="_media_upload")
     * @Route("/media/upload/{album_id}", name="_media_upload_album")
     */
    public function uploadAction($album_id = false)
    {
     $request = $this->getRequest();
     $response = array();
     foreach($this->getRequest()->files->get('files') as $file)
     {
       $media = new Media();
       $media->file = $file;
       $media->setUser($this->get('security.context')->getToken()->getUser());
       if($album_id > 0)
       {
         $album = $this->getDoctrine()->getRepository('YaigAlbumBundle:Album')->find($album_id);
         $media->addAlbum($album);
       }
       $media->upload();
       
       $em = $this->getDoctrine()->getEntityManager();
       $em->persist($media);
       $em->flush();
       
       $rfile = array();
       $rfile['fileName'] = $file->getClientOriginalName();
       $rfile['filePath'] = $media->getWebPath('thumb');
       $response[] = $rfile;
     }
    
     
     
     return new Response(json_encode($response));
    }
}
