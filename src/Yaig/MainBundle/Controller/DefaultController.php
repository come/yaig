<?php

namespace Yaig\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="_homepage")
     * @Template()
     */
    public function indexAction()
    {
        $last_albums =  $this->getDoctrine()
                        ->getRepository('YaigAlbumBundle:Album')
                        ->findAll();
        return array('last_albums' => $last_albums);
    }
}
