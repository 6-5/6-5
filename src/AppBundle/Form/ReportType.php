<?php

namespace AppBundle\Form;

use AppBundle\Entity\User;
use AppBundle\Manager\UserManager;
use AppBundle\Repository\ReportRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReportType extends AbstractType
{
    private $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
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
            ->add('urgency', null, [
                'label' => 'report.urgency',
            ])
            ->add('classification', null, [
                'label' => 'report.classification',
            ])
            ->add('addressedTo', EntityType::class, [
                'class' => 'AppBundle\Entity\User',
                'choice_label' => function (User $user) { return $this->userManager->getFullName($user, true); },
                'label' => 'report.addressedTo',
            ])
            ->add('isHierarchical', CheckboxType::class, [
                'label' => 'report.isHierarchical',
            ])
            ->add('files', CollectionType::class, [
                'entry_type' => FileType::class,
                'label' => 'report.files',
            ])
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
