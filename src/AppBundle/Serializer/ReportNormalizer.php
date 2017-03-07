<?php

namespace AppBundle\Serializer;

use AppBundle\Entity\Report;
use AppBundle\Manager\ReportManager;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;

class ReportNormalizer implements NormalizerInterface, SerializerAwareInterface
{
    private $rm;
    private $router;

    use SerializerAwareTrait;

    public function __construct(RouterInterface $router, ReportManager $rm)
    {
        $this->rm = $rm;
        $this->router = $router;
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
            'created_by' => $object->getCreatedBy()->getUsername(),
            'addressed_to' => ($u = $object->getAddressedTo()) ? $u->getUsernameCanonical() : '',
            'started_at' => ($d = $object->getStartedAt()) ? $d->format('d.m.Y H:i') : '',
            'urgency' => $object->getUrgency(),
            'classification' => $object->getClassification(),
            'status' => $object->getStatus(),
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