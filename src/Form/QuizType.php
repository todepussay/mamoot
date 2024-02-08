<?php

namespace App\Form;

use App\Entity\Quiz;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\Length;

class QuizType extends AbstractType
{

    public TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add("title", TextType::class, [
                "label" => $this->translator->trans("Titre du quiz"),
                "constraints" => [
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'Le titre du quiz ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('image', FileType::class, [
                'required' => false,
                'label' => 'Image',
            ])
            ->addEventListener(FormEvents::SUBMIT, [$this, 'onSubmit']);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Quiz::class,
        ]);
    }

    public function onSubmit(FormEvent $event): void
    {
        $form = $event->getForm();
        $data = $form->getData();

        $imageFile = $data->getImage();

        if ($imageFile) {
            $timestamp = time();
            $quizTitle = $data->getTitle();

            $newFilename = $timestamp . '_' . $quizTitle . '.' . $imageFile->guessExtension();

            $imageFile->move(
                'img/quiz',
                $newFilename
            );

            $data->setImage($newFilename);
        }
    }
}
