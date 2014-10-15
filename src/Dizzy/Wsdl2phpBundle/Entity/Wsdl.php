<?php
namespace Dizzy\Wsdl2phpBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Wsdl {

    /**
     * @Assert\Url()
     */
    protected $path;

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

} 