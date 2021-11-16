<?php

namespace App\Form;

use App\Entity\Trick;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                "label" => "Nom du produit",
                "attr"  => ['placeholder' => 'Veuillez entrer le nom de la figure']
            ])
            ->add('description')
            ->add('created_at')
            ->add('slug')
            ->add('category', EntityType::class, [
                "label" => "Catégorie",
                "placeholder" => "-- Choisir une catégorie --",
                "class" => Category::class,
                "choice_label" => function (Category $category) {
                    return $category->getName();
                }
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
