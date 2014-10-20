<?php


namespace Dizzy\Wsdl2phpBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class WsdlType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('path','text',['label'=>'Wsdl url (with http(s)://)','required'=>false])
            ->add('file','file',['label'=>'Or upload file directly','required'=>false])
            ->add('namespace','text',['label'=>'Namespace','required'=>false])
            ->add('generate','submit')
        ;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'wsdl';
    }

} 