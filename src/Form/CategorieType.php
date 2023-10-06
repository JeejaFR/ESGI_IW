<?php

namespace App\Form;

use App\Entity\Categorie;
use Symfony\Bundle\MakerBundle\Doctrine\RelationManyToMany;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategorieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre:',
                'required' => true,
                'attr' => [
                    'placeholder' => "Titre de votre article"
                ] 
            ])
            ->add('enabled',CheckboxType::class,[
                'label' => 'Actif',
                'required' => false,
                'data' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Categorie::class,
        ]);
    }
}
