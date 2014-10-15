<?php
namespace Dizzy\Wsdl2phpBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Wsdl {

    /**
     * @Assert\Url()
     * @Assert\NotBlank()
     */
    protected $path;

    protected $nameSpace;

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

    /**
     * @return mixed
     */
    public function getNameSpace()
    {
        return $this->nameSpace;
    }

    /**
     * @param mixed $nameSpace
     */
    public function setNameSpace($nameSpace)
    {
        $this->nameSpace = $nameSpace;
    }



} 