<?php

namespace App\Form;

use App\Entity\Trick;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                "label" => "Name",
                "attr"  => ['placeholder' => 'Please enter a name']
            ])
            ->add('description', TextareaType::class, [
                "label" => "Description",
                "attr"  => ["placeholder" => "Please enter a description"]
            ])
            ->add('mainPicture', FileType::class, [
                'required' => false,
                'multiple' => false,
                'mapped' => false
            ])
            ->add('category', EntityType::class, [
                "label" => "Catégory",
                "placeholder" => "-- Choose category --",
                "class" => Category::class,
                "choice_label" => function (Category $category) {
                    return strtoupper($category->getName());
                }
            ])
            ->add('pictures', FileType::class, [
                "label" => "Pictures",
                "multiple" => true,
                "mapped" => false,
                'required' => false
            ])
            ->add('videos', CollectionType::class, [
                'label' => "Videos",
                'entry_type' => VideoType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
