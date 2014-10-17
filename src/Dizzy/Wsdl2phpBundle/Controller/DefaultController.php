<?php

namespace Dizzy\Wsdl2phpBundle\Controller;


use Dizzy\Wsdl2phpBundle\Entity\Wsdl;
use Dizzy\Wsdl2phpBundle\Form\WsdlType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Wsdl2PhpGenerator\Config;
use ZipArchive;

class DefaultController extends Controller
{

    private $tempPath;

    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $wsdl = new Wsdl();
        $form = $this->createForm(new WsdlType(), $wsdl);

        $form->handleRequest($request);

        $file = false;

        if ($form->isValid()) {
            $this->tempPath = realpath($_SERVER['DOCUMENT_ROOT'] . '/../tmp/') . '/';
            $rand           = $this->getTempPath();
            $folder         = $this->tempPath . $rand;


            $url = $form->get('path')->getData();
            $namespace = ($form->get('namespace')->getData()) ?: false;

            $generator = new \Wsdl2PhpGenerator\Generator();
            $generator->generate(
                new Config(
                    $url,
                    $folder,
                    false,
                    false,
                    false,
                    false,
                    $namespace,
                    array(),
                    '',
                    '',
                    '',
                    '',
                    '',
                    true,
                    true,
                    false,
                    false
                )
            );

            $this->compress($rand);

            $file = $rand;

        }

        return [
            'form' => $form->createView(),
            'file' => $file
        ];
    }

    private function getTempPath()
    {
        $rand = sha1(microtime());
        $path = $this->tempPath . $rand;
        mkdir($path, 0700, true);
        return $rand;
    }

    private function compress($folder)
    {
        $zip = new \ZipArchive();
        $zip->open($_SERVER['DOCUMENT_ROOT'].'/out/'. $folder . '.zip',ZIPARCHIVE::CREATE);
        $dir = scandir($this->tempPath.$folder);
        foreach ($dir as $file) {
            if(strpos($file,'.')===0){
                continue;
            }
            $zip->addFile($this->tempPath.$folder.'/'.$file,$file);
        }

        $zip->close();
    }
}
