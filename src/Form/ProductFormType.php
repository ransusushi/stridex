<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<Product>
 */
class ProductFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Product Name',
                'attr' => [
                    'placeholder' => 'e.g. StrideX Raptor',
                ],
            ])
            ->add('sku', TextType::class, [
                'label' => 'SKU Code',
                'attr' => [
                    'placeholder' => 'e.g. SX-RAPTOR-001',
                ],
            ])
            ->add('color', TextType::class, [
                'label' => 'Colorway',
                'required' => false,
                'empty_data' => 'Monochrome',
                'attr' => [
                    'placeholder' => 'e.g. Monochrome / Midnight Red',
                ],
            ])
            ->add('category', ChoiceType::class, [
                'label' => 'Category',
                'choices' => Product::CATEGORIES,
            ])
            ->add('gender', ChoiceType::class, [
                'label' => 'Gender / Fit',
                'required' => false,
                'empty_data' => 'unisex',
                'choices' => Product::GENDERS,
            ])
            ->add('badge', ChoiceType::class, [
                'label' => 'Storefront Badge',
                'required' => false,
                'choices' => Product::BADGES,
            ])
            ->add('image', ChoiceType::class, [
                'label' => 'Product Image',
                'required' => false,
                'empty_data' => 'image/urban-collection.png',
                'choices' => [
                    'StrideX Raptor (Monochrome)' => 'image/products/prod_6aa46461dfd1d.png',
                    'StrideX Retro (Rose Gold)' => 'image/products/prod_6aa46482e8d59.png',
                    'StrideX Shift (Pure White)' => 'image/products/prod_6aa464ea4de79.png',
                    'StrideX Drive (Pearl Gray)' => 'image/men-drive.png',
                    'StrideX Luna (Carbon Gray)' => 'image/products/prod_6aa849cbcb00a.png',
                    'StrideX Aura (Cloud White)' => 'image/products/prod_6aa84a0d6036f.png',
                    'StrideX Urban (Midnight Red)' => 'image/urban-collection.png',
                    'StrideX Performance' => 'image/performance-collection.png',
                    'StrideX Heritage' => 'image/heritage-collection.png',
                    'StrideX Mission' => 'image/mission-shoes.png',
                ],
            ])
            ->add('price', NumberType::class, [
                'label' => 'Unit Price ($)',
                'scale' => 2,
                'html5' => true,
                'attr' => [
                    'step' => '0.01',
                    'min' => '0',
                    'placeholder' => '89.99',
                ],
            ])
            ->add('stockQuantity', IntegerType::class, [
                'label' => 'Stock Quantity',
                'attr' => [
                    'min' => '0',
                    'placeholder' => '10',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'rows' => 4,
                    'placeholder' => 'Describe cushioning, upper materials, traction, and fit...',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
