<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Report;
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
        $report = $this->get('app.report_manager')->createReport($this->getUser());
        $form = $this->createForm('AppBundle\Form\ReportType', $report);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($report);
            $em->flush($report);

            return $this->redirectToRoute('report_show', array('reference' => $report->getReference()));
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
     * @Method("GET")
     */
    public function showAction(Report $report)
    {
        return $this->render('report/show.html.twig', array('report' => $report));
    }

    /**
     * Displays a form to edit an existing report entity.
     *
     * @Route("/{reference}/edit", name="report_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Report $report)
    {
        $deleteForm = $this->createDeleteForm($report);
        $editForm = $this->createForm(ReportType::class, $report);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('report_edit', array('reference' => $report->getReference()));
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
