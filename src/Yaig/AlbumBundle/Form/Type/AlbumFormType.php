<?php

namespace Yaig\AlbumBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class AlbumFormType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('name');
    }
    
    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Yaig\AlbumBundle\Entity\Album',
        );
    }

    public function getName()
    {
        return 'album';
    }
}
