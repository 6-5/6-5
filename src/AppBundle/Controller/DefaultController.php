<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $repo = $this->getDoctrine()->getRepository('AppBundle:Report');
        $reportsInProcess = $repo->findByInProcess($this->getUser());
        $reportsUnread = $repo->findByUnread($this->getUser());

        return $this->render('default/index.html.twig', [
            'reports_in_progress' => $reportsInProcess,
            'reports_unread' => $reportsUnread,
        ]);
    }
}
