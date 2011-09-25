<?php

namespace Yaig\MediaBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;



/**
 * @ORM\Entity
 * @ORM\Table(name="media")
 */
class Media
{
    public function __construct()
    {
        $this->albums = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="Yaig\UserBundle\Entity\User", inversedBy="medias")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *
     * @var User $user
     */
    protected $user;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $name = "photo";
    
    /**
     * @ORM\ManyToMany(targetEntity="Yaig\AlbumBundle\Entity\Album", inversedBy="media")
     * @ORM\JoinTable(name="album_media",
     *   joinColumns={
     *     @ORM\JoinColumn(name="media_id", referencedColumnName="id", onDelete="cascade")
     *   }
     * )
     */
    protected $albums;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $path;
    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    public $exif;

    /**
     * @Assert\File(maxSize="6000000")
     */
    public $file;
    
    public function upload()
    {
        // the file property can be empty if the field is not required
        if (null === $this->file) {
            return;
        }

        // we use the original file name here but you should
        // sanitize it at least to avoid any security issues
        
        // set the path property to the filename where you'ved saved the file
        $fname = uniqid().$this->file->getClientOriginalName();
        $this->setPath($fname);
        
        // move takes the target directory and then the target filename to move to
        $this->file->move($this->getUploadRootDir('/origin/'), $fname);
        
        $imagine = new \Imagine\Gd\Imagine();
        $image = $imagine->open($this->getAbsolutePath('/origin/'));
        if($exif = @exif_read_data($this->getAbsolutePath('/origin/')))
        {
          if(isset($exif['Orientation']) and $orientation = $exif['Orientation'])
          {
            $image = $this->computeExifOrientation($image, $orientation);
          }
          
          if(isset($exif['FileName']) and $exif['FileName'] and !$this->getName())
          {
            $this->setName($exif['FileName']);
          }
          $this->setExif(json_encode($exif));
        }

        $image->save($this->getAbsolutePath('/origin/'));
        $size = new \Imagine\Image\Box(800,800);
        $thumb = $image->thumbnail($size, \Imagine\Image\ImageInterface::THUMBNAIL_INSET); 
        $thumb->save($this->getAbsolutePath('/classic/')); 
        $size = new \Imagine\Image\Box(100,120);
        $thumb = $image->thumbnail($size, \Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND); 
        $thumb->save($this->getAbsolutePath('/thumb/')); 
        
              
        // clean up the file property as you won't need it anymore
        $this->file = null;
    }
    
    private function computeExifOrientation(&$image, $orientation)
    {
      switch ($orientation) {
          case 2:
              $image->flipHorizontally();
              break;
          case 3:
              $image->rotate(180);
              break;
          case 4:
              $image->flipVertically();
              break;
          case 6:
              $image->rotate(-90);
              break;
          case 8:
              $image->rotate(-270);
              break;
      }
      return $image;
    }

    public function getAbsolutePath($size = "")
    {
        return null === $this->path ? null : $this->getUploadRootDir($size).'/'.$this->path;
    }

    public function getWebPath($size = "")
    {
        return null === $this->path ? null : $this->getUploadDir($size).'/'.$this->path;
    }

    public function getUploadRootDir($relative = "")
    {
        // the absolute directory path where uploaded documents should be saved
        return __DIR__.'/../../../../web/'.$this->getUploadDir($relative);
    }

    protected function getUploadDir($relative = "")
    {
        $dir_path = $this->getDirPath($relative);
        return $dir_path;
    }
    
    public function getDirPath($rel = null, $mkdir = true)
    {
      $md5 = str_split(md5($this->getPath()));
      if($rel)
      {
        $path = '/uploads/medias/'.$rel.'/'.$md5[0].'/'.$md5[1].'/'.$md5[2].'/'.$md5[3].'/'.$md5[4].'/'.$md5[5].'/'.$md5[6].'/'.$md5[7].'/';
      }
      else
      {
        $path = '/uploads/medias/'.$md5[0].'/'.$md5[1].'/'.$md5[2].'/'.$md5[3].'/'.$md5[4].'/'.$md5[5].'/'.$md5[6].'/'.$md5[7].'/';
      }
      
      if(!is_dir(__DIR__.'/../../../../web/'.$path) and $mkdir)
      {
        mkdir(__DIR__.'/../../../../web/'.$path, 0777, true);
      }
      
      return $path;
    }
  
    public function getDirUrl($rel = null, $mkdir = true)
    {
      $md5 = str_split(md5($this->getPath()));
      if($rel)
      {
        $path = '/uploads/medias/'.$rel.'/'.$md5[0].'/'.$md5[1].'/'.$md5[2].'/'.$md5[3].'/'.$md5[4].'/'.$md5[5].'/'.$md5[6].'/'.$md5[7].'/';
      }
      else
      {
        $path = '/uploads/medias/'.$md5[0].'/'.$md5[1].'/'.$md5[2].'/'.$md5[3].'/'.$md5[4].'/'.$md5[5].'/'.$md5[6].'/'.$md5[7].'/';
      }
      
      if(!is_dir(__DIR__.'/../../../../web/'.$path) and $mkdir)
      {
        mkdir(__DIR__.'/../../../../web/'.$path, 0777, true);
      }
      
      return $path;
    }
  
    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;
    

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
     * Set path
     *
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
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
     * Add albums
     *
     * @param Yaig\AlbumBundle\Entity\Album $albums
     */
    public function addAlbum(\Yaig\AlbumBundle\Entity\Album $album)
    {
        $this->albums[] = $album;
    }

    /**
     * Get albums
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getAlbums()
    {
        return $this->albums;
    }


    /**
     * Set exif
     *
     * @param text $exif
     */
    public function setExif($exif)
    {
        $this->exif = $exif;
    }

    /**
     * Get exif
     *
     * @return text 
     */
    public function getExif()
    {
        return json_decode($this->exif);
    }

   
    /**
     * Get user_id
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->user->getId();
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
     * Get user
     *
     * @return Yaig\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
