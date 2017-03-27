<?php

namespace AppBundle\Serializer;

use AppBundle\Entity\Report;
use AppBundle\Manager\ReportManager;
use AppBundle\Manager\UserManager;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;

class ReportNormalizer implements NormalizerInterface, SerializerAwareInterface
{
    private $rm;
    private $router;
    private $userManager;
    private $templating;

    use SerializerAwareTrait;

    public function __construct(RouterInterface $router, ReportManager $rm, UserManager $userManager, TwigEngine $templating)
    {
        $this->rm = $rm;
        $this->router = $router;
        $this->userManager = $userManager;
        $this->templating = $templating;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = array())
    {
        /** @var Report $object */

        return [
            'reference' => $object->getReference(),
            'object' => $object->getObject(),
            'created_by' => $this->userManager->getFullName($object->getCreatedBy()),
            'addressed_to' => ($u = $object->getAddressedTo()) ? $this->userManager->getFullName($u) : '',
            'started_at' => ($d = $object->getStartedAt()) ? $d->format('d.m.Y H:i') : '',
            'urgency' => $object->getUrgency(),
            'classification' => $object->getClassification(),
            'status' => $object->getStatus(),
            'status_rendered' => ($d = $object->getLastDecision()) ? $this->templating->render(':report:_status_inline.html.twig', ['decision' => $d]) : '',
            'path_show' => $this->router->generate('report_show', ['reference' => $object->getReference()]),
            'path_edit' => $this->router->generate('report_edit', ['reference' => $object->getReference()]),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Report;
    }
}
