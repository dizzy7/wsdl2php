<?php

namespace Dizzy\Wsdl2phpBundle\Controller;


use Dizzy\Wsdl2phpBundle\Entity\Wsdl;
use Dizzy\Wsdl2phpBundle\Form\WsdlType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\UrlValidator;
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

        $fileData = false;

        if ($form->isValid()) {

            /** @var UploadedFile $fileData */
            $fileData = $form->get('file')->getData();
            $pathData = $form->get('path')->getData();

            if ($fileData || $pathData) {
                $this->tempPath = realpath($_SERVER['DOCUMENT_ROOT'] . '/../tmp/') . '/';
                $rand           = $this->getTempPath();
                $folder         = $this->tempPath . $rand;

                if ($fileData) {
                    $fileData->move($folder, 'wsdl.xml');
                    $xml = file_get_contents($folder . '/wsdl.xml');
                } else {
                    $xml = file_get_contents($pathData);
                    file_put_contents($folder . '/wsdl.xml', $xml);
                }
                
                libxml_use_internal_errors(true);
                $xmlTest = simplexml_load_string($xml);
                if ($xmlTest === false) {
                    $form->addError(new FormError('Invalid xml file'));
                } else {
                    $namespace = ($form->get('namespace')->getData()) ?: false;

                    $generator = new \Wsdl2PhpGenerator\Generator();
                    $generator->generate(
                        new Config(
                            $folder . '/wsdl.xml',
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

                    $fileData = $rand;
                }

            } else {
                $form->addError(new FormError('You must specify the url or upload a file'));
            }
        }

        return [
            'form' => $form->createView(),
            'file' => $fileData
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
        $zip->open($_SERVER['DOCUMENT_ROOT'] . '/out/' . $folder . '.zip', ZIPARCHIVE::CREATE);
        $dir = scandir($this->tempPath . $folder);
        foreach ($dir as $file) {
            if (strpos($file, '.') === 0) {
                continue;
            }
            $zip->addFile($this->tempPath . $folder . '/' . $file, $file);
        }

        $zip->close();
    }
}
