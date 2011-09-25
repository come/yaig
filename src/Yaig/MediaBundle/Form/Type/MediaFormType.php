<?php

namespace Yaig\MediaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class MediaFormType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('name')
                ->add('description')
                ->add('file')
                ->add('albums', 'entity', array(
                    'class' => 'Yaig\\AlbumBundle\\Entity\\Album',
                    'multiple' => true,
                    'expanded' => true,
                    'property' => 'name',
                 ));
    }
    
    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Yaig\MediaBundle\Entity\Media',
        );
    }

    public function getName()
    {
        return 'media';
    }
}
