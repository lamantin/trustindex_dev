<?php

namespace App\Form;

use App\Entity\Review;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Miért adunk hozzá explicit típusokat és HTML5 attribútumokat?
        // A jobb UX és a beépített böngészős validáció miatt.
        $builder
            ->add('companyName', TextType::class, [
                'label' => 'Cég neve',
                'attr' => ['placeholder' => 'Pl. Trustindex Kft.', 'class' => 'form-control'],
            ])
            ->add('rating', IntegerType::class, [
                'label' => 'Értékelés (1-5)',
                'attr' => ['min' => 1, 'max' => 5, 'class' => 'form-control'],
            ])
            ->add('reviewText', TextareaType::class, [
                'label' => 'Vélemény szövege',
                'attr' => ['rows' => 5, 'placeholder' => 'Írd le a tapasztalataidat...', 'class' => 'form-control'],
            ])
            ->add('authorEmail', EmailType::class, [
                'label' => 'Az Te email címed',
                'attr' => ['placeholder' => 'pelda@email.com', 'class' => 'form-control'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}
