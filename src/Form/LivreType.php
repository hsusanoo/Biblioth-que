<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Descripteur;
use App\Entity\Livre;
use App\Form\DataTransformer\StringToTagTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LivreType extends AbstractType
{

    private $transformer;

    public function __construct(StringToTagTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('couverture')
            ->add('titrePrincipale')
            ->add('titreSecondaire')
            ->add('dateEdition', TextType::class)
            ->add('prix')
            ->add('observation')
            ->add('Isbn')
            ->add('dateAquis', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd/MM/yyyy'
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => "nom"
            ])
            ->add('descripteurs', ChoiceType::class, [
                'label' => "Mots clés",
                'multiple' => true,
                'required' => false,
                'attr' => [
                    'class' => "select-two-multiple"
                ]
            ])
            ->add('exemplaires', CollectionType::class, [
                'entry_type' => ExemplaireType::class,
                'entry_options' => [
                    'label' => false
                ],
                'allow_add' => true,
                'allow_delete' => true
            ])
            ->add('auteurs', CollectionType::class, [
                'entry_type' => AuteurType::class,
                'entry_options' => [
                    'label' => false
                ],
                'allow_add' => true,
                'allow_delete' => true
            ]);
        $builder->get('descripteurs')->resetViewTransformers();
//        $builder->get('descripteurs')->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Livre::class,
        ]);
    }
}
