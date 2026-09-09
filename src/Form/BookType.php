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
            ->add('submit', SubmitType::class, [
            'label' => $options['submit_label'],
])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
            'submit_label' => 'Ajouter',
        ]);
    }
}
