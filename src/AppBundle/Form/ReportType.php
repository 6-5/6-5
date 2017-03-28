<?php

namespace AppBundle\Form;

use AppBundle\Entity\User;
use AppBundle\Manager\ReportManager;
use AppBundle\Manager\UserManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReportType extends AbstractType
{
    private $userManager;
    private $reportManager;

    public function __construct(UserManager $userManager, ReportManager $reportManager)
    {
        $this->userManager = $userManager;
        $this->reportManager = $reportManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('object', TextType::class, [
                'label' => 'report.object',
            ])
            ->add('message', TextareaType::class, [
                'label' => 'report.message',
            ])
            ->add('startedAt', DateTimeType::class, [
                'html5' => false,
                'date_widget' => 'single_text',
                'time_widget' => 'single_text'
            ])
            ->add('place', TextType::class, [
                'label' => 'report.place',
            ])
            ->add('urgency', ChoiceType::class, [
                'label' => 'report.urgency',
                'choices' => array_flip($this->reportManager->getUrgencies()),
            ])
            ->add('classification', ChoiceType::class, [
                'label' => 'report.classification',
                'choices' => array_flip($this->reportManager->getClassifications()),
            ])
            ->add('addressedTo', EntityType::class, [
                'class' => 'AppBundle\Entity\User',
                'choice_label' => function (User $user) { return $this->userManager->getFullName($user, true); },
                'label' => 'report.addressedTo',
                'attr' => [
                    'data-live-search' => true,
                    'data-size' => 5,
                ],
                'group_by' => function(User $user, $key, $index) {
                    return ucfirst($this->userManager->getRanks()[$user->getRank()]);
                },
            ])
            /*
            ->add('isHierarchical', CheckboxType::class, [
                'label' => 'report.isHierarchical',
            ])
            ->add('files', CollectionType::class, [
                'entry_type' => FileType::class,
                'label' => 'report.files',
            ])
            */
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Report'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_report';
    }
}
