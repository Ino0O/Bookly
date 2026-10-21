<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Book;
use App\Entity\Genre;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Enum\BookStatus;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\Image;

class BookType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('author')
            ->add('status', EnumType::class, [
            'class' => BookStatus::class,
            'choice_label' => fn (BookStatus $status) => $status->value,
            ])
            ->add('genre', EntityType::class, [
                'class' => Genre::class,
                'choice_label' => 'name',
            ])
            ->add('image', FileType::class, [
            'label' => 'Image de couverture',
            'mapped' => false,
            'required' => false,
            'constraints' => [
                new Image(
                maxSize: '5M',
                mimeTypes: [
                    'image/jpeg',
                    'image/png',
                    'image/webp',
            ],
                mimeTypesMessage: 'Veuillez sélectionner une image JPEG, PNG ou WebP.',
        ),
    ],
])
        ->add('submit', SubmitType::class, [
                    'label' => $options['submit_label'],
                    ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
            'submit_label' => 'Ajouter',
        ]);
    }
}
