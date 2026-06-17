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
use App\Form\DataTransformer\TagsDataTransformer;
use App\Entity\Tag;

class ElementType extends AbstractType{
    public function __construct(private readonly TagsDataTransformer $tagsDataTransformer)
    {
    }
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
        'label' => 'label.author',
        'required' => false,
    ])
    ->add('year', IntegerType::class, [
    'label' => 'label.year',
    'required' => false,
])
    ->add('category', EntityType::class, [
        'class' => Category::class,
        'choice_label' => 'name',
        'label' => 'label.category',
        'required' => true,
        'placeholder' => 'placeholder.choose_category',
    ])
    ->add(
        'tags',
        TextType::class,
        [
            'label' => 'label.tags',
            'required' => false,
            'attr' => ['max_length' => 128],
        ]
    )

        ->get('tags')->addModelTransformer(
            $this->tagsDataTransformer
        );
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
