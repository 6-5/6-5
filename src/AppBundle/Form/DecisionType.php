<?php

namespace AppBundle\Form;

use AppBundle\Entity\User;
use AppBundle\Manager\UserManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DecisionType extends AbstractType
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
            ->add('comment', TextareaType::class, [
                'label' => 'decision.comment',
            ])
            ->add('transfertTo', EntityType::class, [
                'class' => 'AppBundle\Entity\User',
                'label' => false,
                'choice_label' => function (User $user) { return $this->userManager->getFullName($user, true); },
                'mapped' => false,
                'attr' => [
                    'data-live-search' => true,
                    'data-size' => 10,
                ],
                'group_by' => function(User $user, $key, $index) {
                    return ucfirst($this->userManager->getRanks()[$user->getRank()]);
                },
            ])
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Decision'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_decision';
    }


}
