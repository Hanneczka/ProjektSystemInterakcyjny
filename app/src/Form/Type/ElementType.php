<?php

namespace App\Form\Type;

use App\Entity\Element;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Category;
use App\Entity\Tag;

class ElementType extends AbstractType{
    public function buildForm(FormBuilderInterface $builder, array $options): void
{
$builder
    ->add(
    'title',
    TextType::class,
    [
    'label' => 'label.title',
    'required' => true,
    'attr' => ['max_length' => 64],
    ])
    ->add('author', TextType::class, [
        'label' => 'Autor',
        'required' => false,
    ])
    ->add('year', IntegerType::class, [
    'label' => 'Rok wydania',
    'required' => false,
])
    ->add('category', EntityType::class, [
        'class' => Category::class,
        'choice_label' => 'name',
        'label' => 'Kategoria',
        'required' => true,
        'placeholder' => '--- Wybierz kategorię ---',
    ])
    ->add('tags', EntityType::class, [
        'class' => Tag::class,
        'choice_label' => 'title',
        'multiple' => true,
        'label' => 'Tag',
        'required' => false,
        'expanded' => false,
    ]);
    }

public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Element::class]);
    }
    public function getBlockPrefix(): string
{
    return 'element';
}
}
