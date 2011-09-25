<?php

namespace Yaig\AlbumBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="album")
 */
class Album
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="Yaig\UserBundle\Entity\User")
     */
    protected $user;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $name;
    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;
    
     /**
     * @var Medias
     *
     * @ORM\ManyToMany(targetEntity="Yaig\MediaBundle\Entity\Media", mappedBy="albums")
     * @ORM\JoinTable(name="album_media",
     *   joinColumns={
     *     @ORM\JoinColumn(name="album_id", referencedColumnName="id", onDelete="cascade")
     *   }
     * )
     */
    private $medias;
    
    public function __construct()
    {
        $this->medias = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param text $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return text 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Add medias
     *
     * @param Yaig\AlbumBundle\Entity\Media $medias
     */
    public function addMedia(\Yaig\AlbumBundle\Entity\Media $medias)
    {
        $this->medias[] = $medias;
    }

     /**
     * Get picture
     *
     * @return Yaig\MediaBundle\Entity\Media
     */
    public function getPicture()
    {
      $medias = $this->getMedias();
      if(count($medias) > 0)
      {
      
        return $medias[0];
      }
      else
      {
        return new \Yaig\MediaBundle\Entity\Media();
      }
    }

    /**
     * Get medias
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getMedias()
    {
        return $this->medias;
    }

    /**
     * Add user
     *
     * @param Yaig\UserBundle\Entity\User $user
     */
    public function addUser(\Yaig\UserBundle\Entity\User $user)
    {
        $this->user[] = $user;
    }

    /**
     * Get user
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set user
     *
     * @param Yaig\UserBundle\Entity\User $user
     */
    public function setUser(\Yaig\UserBundle\Entity\User $user)
    {
        $this->user = $user;
    }

    /**
     * Get media
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getMedia()
    {
        return $this->media;
    }
}
