<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'security.registration_form.email',
                'attr' => [
                    'placeholder' => 'security.registration_form.email',
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                    'placeholder' => 'security.registration_form.password',
                ],
                'constraints' => $this->getPasswordConstraints(),
                'label' => 'security.registration_form.password',
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => $this->getAgreeTermsConstraints(),
                'label' => 'security.registration_form.agree_terms',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

    /**
     * @return Constraint[]
     */
    protected function getPasswordConstraints(): array
    {
        return [
            new NotBlank([
                'message' => 'registration_form.password.not_blank',
            ]),
            new Length([
                'min' => 6,
                'minMessage' => 'registration_form.password.min',
                'max' => 4096, // max length allowed by Symfony for security reasons
            ]),
        ];
    }

    /**
     * @return Constraint[]
     */
    protected function getAgreeTermsConstraints(): array
    {
        return [
            new IsTrue([
                'message' => 'registration_form.agree_terms.is_true',
            ]),
        ];
    }
}
