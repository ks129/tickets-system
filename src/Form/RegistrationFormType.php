<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'row_attr' => ['class' => 'mb-3 form-floating'],
                'attr' => ['placeholder' => ''],
                'label' => 'E-mail address',
                'translation_domain' => 'auth',
            ])
            ->add('firstName', TextType::class, [
                'row_attr' => ['class' => 'mb-3 form-floating'],
                'attr' => ['placeholder' => ''],
                'label' => 'First name',
                'translation_domain' => 'auth',
            ])
            ->add('lastName', TextType::class, [
                'row_attr' => ['class' => 'mb-3 form-floating'],
                'attr' => ['placeholder' => ''],
                'label' => 'Last name',
                'translation_domain' => 'auth',
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password', 'placeholder' => ''],
                'row_attr' => ['class' => 'mb-3 form-floating'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
                'label' => 'Password',
                'translation_domain' => 'auth',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}