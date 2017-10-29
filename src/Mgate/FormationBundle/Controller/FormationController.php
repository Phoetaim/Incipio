<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Florian Lefevre
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mgate\FormationBundle\Controller;

use Mgate\FormationBundle\Entity\Formation;
use Mgate\FormationBundle\Form\Type\FormationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FormationController extends Controller
{
    /**
     * @Security("has_role('ROLE_CA')")
     * Display a list of all training given order by date desc
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $formations = $em->getRepository('MgateFormationBundle:Formation')->getAllFormations([], ['dateDebut' => 'DESC']);

        return $this->render('MgateFormationBundle:Gestion:index.html.twig', [
            'formations' => $formations,
        ]);
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     * Display a list of all training group by term.
     */
    public function listerAction()
    {
        $em = $this->getDoctrine()->getManager();
        $formationsParMandat = $em->getRepository('MgateFormationBundle:Formation')->findAllByMandat();

        return $this->render('MgateFormationBundle:Formations:lister.html.twig', [
            'formationsParMandat' => $formationsParMandat,
        ]);
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     *
     * @param Formation $formation The training to display
     *
     * @return Response
     *                  Display a training
     */
    public function voirAction(Formation $formation)
    {
        return $this->render('MgateFormationBundle:Formations:voir.html.twig', [
            'formation' => $formation,
        ]);
    }

    /**
     * @Security("has_role('ROLE_CA')")
     *
     * @param $id mixed valid id : modify an existing training; unknown id : display a creation form
     *
     * @return Response
     *                  Manage creation and update of a training
     */
    public function modifierAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$formation = $em->getRepository('Mgate\FormationBundle\Entity\Formation')->find($id)) {
            $formation = new Formation();
        }

        $form = $this->createForm(FormationType::class, $formation);

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em->persist($formation);
                $em->flush();

                $form = $this->createForm(FormationType::class, $formation);
                $this->addFlash('success', 'Formation modifiée');
            } else {
                //constitution du tableau d'erreurs
                $errors = $this->get('validator')->validate($formation);
                foreach ($errors as $error) {
                    $this->addFlash('warning', $error->getPropertyPath() . ' : ' . $error->getMessage());
                }
            }
        }

        return $this->render('MgateFormationBundle:Gestion:modifier.html.twig', ['form' => $form->createView(),
            'formation' => $formation,
        ]);
    }

    /**
     * @Security("has_role('ROLE_CA')")
     *
     * @param Request $request
     *
     * @return Response
     *                  Manage particpant present to a training
     */
    public function participationAction(Request $request, $mandat = null)
    {
        $em = $this->getDoctrine()->getManager();
        $formationsParMandat = $em->getRepository('MgateFormationBundle:Formation')->findAllByMandat();

        $choices = [];
        foreach ($formationsParMandat as $key => $value) {
            $choices[$key] = $key;
        }

        $defaultData = [];
        $form = $this->createFormBuilder($defaultData)
            ->add(
                'mandat',
                ChoiceType::class,
                [
                    'label' => 'Présents aux formations du mandat ',
                    'choices' => $choices,
                    'required' => true,
                ]
            )->getForm();

        if (null !== $mandat) {
            $formations = array_key_exists($mandat, $formationsParMandat) ? $formationsParMandat[$mandat] : [];
        } else {
            $formations = count($formationsParMandat) ? reset($formationsParMandat) : [];
        }

        $presents = [];

        foreach ($formations as $formation) {
            foreach ($formation->getMembresPresents() as $present) {
                $id = $present->getPrenomNom();
                if (array_key_exists($id, $presents)) {
                    $presents[$id][] = $formation->getId();
                } else {
                    $presents[$id] = [$formation->getId()];
                }
            }
        }

        return $this->render('MgateFormationBundle:Gestion:participation.html.twig', [
            'form' => $form->createView(),
            'formations' => $formations,
            'presents' => $presents,
            'mandat' => $mandat,
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Formation $formation The training to delete (paramconverter from id)
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *                                                            Delete a training
     */
    public function supprimerAction(Formation $formation)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($formation);
        $em->flush();

        return $this->redirectToRoute('Mgate_formations_lister', []);
    }
}
