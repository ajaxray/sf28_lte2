<?php

namespace AppBundle\Entity;

use AppBundle\Helper\Dir;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serialize;

/**
 * Avatar
 *
 * @Gedmo\Uploadable(pathMethod="getFilePath", filenameGenerator="ALPHANUMERIC", appendNumber=true)
 * @ORM\Table(name="avatar")
 * @ORM\Entity()
 */
class Avatar
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="path", type="string")
     * @Gedmo\UploadableFilePath
     */
    private $path;

    private $uploadDir = 'uploads/avatars';

    /**
     * This function will be used by Uploadable Gedmo extention to get file path
     */
    public function getFilePath()
    {
        global $kernel;
        $path = $kernel->getRootDir() . "/../web/{$this->uploadDir}/";

        if(! is_dir($path)) {
            Dir::makeWritable($path);
        }

        return $path;
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
     * Get path
     *
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set size
     *
     * @param mixed $size
     *
     * @return Avatar
     */
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

    public function getUrl()
    {
        return Dir::pathToUrl($this->getPath());
    }
}
