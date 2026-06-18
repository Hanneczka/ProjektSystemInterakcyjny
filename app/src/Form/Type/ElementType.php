<?php

/**
 * Element type.
 */

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

/**
 * Class ElementType.
 */
class ElementType extends AbstractType
{
    /**
     * Constructor.
     *
     * @param TagsDataTransformer $tagsDataTransformer Tags data transformer
     */
    public function __construct(private readonly TagsDataTransformer $tagsDataTransformer)
    {
    }

    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting from the
     * top most type. Type extensions can further modify the form.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array<string, mixed> $options Form options
     *
     * @see FormTypeExtensionInterface::buildForm()
     */
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
                ]
            )
            ->add(
                'author',
                TextType::class,
                [
                    'label' => 'label.author',
                    'required' => false,
                ]
            )
            ->add(
                'year',
                IntegerType::class,
                [
                    'label' => 'label.year',
                    'required' => false,
                ]
            )
            ->add(
                'category',
                EntityType::class,
                [
                    'class' => Category::class,
                    'choice_label' => 'name',
                    'label' => 'label.category',
                    'required' => true,
                    'placeholder' => 'placeholder.choose_category',
                ]
            )
            ->add(
                'tags',
                TextType::class,
                [
                    'label' => 'label.tags',
                    'required' => false,
                    'attr' => ['max_length' => 128],
                ]
            );

        $builder->get('tags')->addModelTransformer($this->tagsDataTransformer);
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Element::class]);
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * The block prefix defaults to the underscored short class name with
     * the "Type" suffix removed (e.g. "UserProfileType" => "user_profile").
     *
     * @return string The prefix of the template block name
     */
    public function getBlockPrefix(): string
    {
        return 'element';
    }
}
