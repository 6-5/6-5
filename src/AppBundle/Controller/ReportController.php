<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Report;
use AppBundle\Form\DecisionType;
use AppBundle\Form\ReportType;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Report controller.
 *
 * @Route("report")
 */
class ReportController extends Controller
{
    /**
     * Lists all sent report entities.
     *
     * @Route("/sent", name="report_index_sent")
     * @Method("GET")
     */
    public function indexSentAction()
    {
        return $this->render('report/index_sent.html.twig');
    }

    /**
     * Lists all draft report entities.
     *
     * @Route("/draft", name="report_index_draft")
     * @Method("GET")
     */
    public function indexDraftAction()
    {
        return $this->render('report/index_draft.html.twig');
    }

    /**
     * Lists all received report entities.
     *
     * @Route("/received", name="report_index_received")
     * @Method("GET")
     */
    public function indexReceivedAction()
    {
        return $this->render('report/index_received.html.twig');
    }

    /**
     * @Route("/data", name="report_index_data")
     * @Method("POST")
     */
    public function indexDataAction(Request $request)
    {
        $firstResult = $request->get('start');
        $createdBy = $request->query->has('received') ? null : $this->getUser();
        $addressedTo = $request->query->has('received') ? $this->getUser() : null;
        $isDraft = $request->query->has('is_draft');
        $repo = $this->getDoctrine()->getRepository('AppBundle:Report');

        $query = $repo->getQueryForPagination($createdBy, $isDraft, $addressedTo, $firstResult);
        $reports = new Paginator($query, $fetchJoinCollection = true);
        $data = ['recordsTotal' => $reports->count(), 'recordsFiltered' => $reports->count(), 'data' => $reports];

        return $this->json($data);
    }

    /**
     * Creates a new report entity.
     *
     * @Route("/new", name="report_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $isDraft = $request->request->has('save_as_draft');
        $report = $this->get('app.report_manager')->createReport($this->getUser());
        $form = $this->createForm(ReportType::class, $report, ['validation_groups' => $isDraft ? 'Draft' : 'Default']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $rm = $this->get('app.report_manager');
            $rm->saveAsDraft($report);
            $report = $isDraft ? $report : $rm->addressTo($report, $report->getAddressedTo());
            $route = $isDraft ? 'report_edit' : 'report_show';
            $message = $isDraft ? 'report.save_as_draft_ok' : 'report.send_ok';

            $em = $this->getDoctrine()->getManager();
            $em->persist($report);
            $em->flush($report);

            $this->addFlash('default', $message);

            return $this->redirectToRoute($route, ['reference' => $report->getReference()]);
        }

        return $this->render('report/new.html.twig', array(
            'report' => $report,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a report entity.
     *
     * @Route("/{reference}", name="report_show")
     * @Route("/{reference}/decide", name="report_decide")
     * @Method({"GET", "POST"})
     */
    public function showAction(Request $request, Report $report)
    {
        $em = $this->get('doctrine')->getManager();
        $rm = $this->get('app.report_manager');

        if (!$report->getLastDecision()->getReadedAt()) {
            $report = $rm->read($report);
            $em->persist($report);
            $em->flush();
        }

        $form = $this->createForm(DecisionType::class, $decision = $report->getLastDecision(), [
            'action' => $this->generateUrl('report_decide', ['reference' => $report->getReference()]),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            switch (true) {
                case $request->request->has('accept'):
                    $report = $rm->decideToAccept($report, $decision->getComment());
                    break;
                case $request->request->has('refuse'):
                    $report = $rm->decideToRefuse($report, $decision->getComment());
                    break;
                case $request->request->has('transfert'):
                    $transfertTo = $form->get('transfertTo')->getData();
                    $report = $rm->decideToTransfer($report, $transfertTo, $decision->getComment());
                    break;
                default:
                    throw new \Exception('Bad action.');
            }

            $em->persist($report);
            $em->flush();

            return $this->redirectToRoute('report_index_received');
        }

        return $this->render('report/show.html.twig', array(
            'report' => $report,
            'decision_form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing report entity.
     *
     * @Route("/{reference}/edit", name="report_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Report $report)
    {
        $isDraft = $request->request->has('save_as_draft');
        $deleteForm = $this->createDeleteForm($report);
        $editForm = $this->createForm(ReportType::class, $report, ['validation_groups' => $isDraft ? 'Draft' : 'Default']);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $rm = $this->get('app.report_manager');
            $rm->saveAsDraft($report);
            $report = $isDraft ? $report : $rm->addressTo($report, $report->getAddressedTo());
            $route = $isDraft ? 'report_edit' : 'report_show';
            $message = $isDraft ? 'report.save_as_draft_ok' : 'report.send_ok';

            $em = $this->getDoctrine()->getManager();
            $em->persist($report);
            $em->flush($report);

            $this->addFlash('default', $message);

            return $this->redirectToRoute($route, ['reference' => $report->getReference()]);
        }

        return $this->render('report/edit.html.twig', array(
            'report' => $report,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a report entity.
     *
     * @Route("/{reference}", name="report_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Report $report)
    {
        $form = $this->createDeleteForm($report);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($report);
            $em->flush($report);
            $this->addFlash('default', 'report.delete_ok');
        }

        return $this->redirectToRoute('report_index_draft');
    }

    /**
     * Creates a form to delete a report entity.
     *
     * @param Report $report The report entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Report $report)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('report_delete', array('reference' => $report->getReference())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
