<?php

namespace App\Controller;

use App\Entity\Matchs;
use App\Form\MatchsType;
use App\Repository\CalendarRepository;
use App\Repository\MatchsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Calendar;



class MatchsController extends AbstractController
{
    /**
     * @Route("/matchs", name="matchs_index", methods={"GET"})
     */
    public function index(MatchsRepository $matchsRepository): Response
    {
        return $this->render('matchs/index.html.twig', [
            'matchs' => $matchsRepository->findAll(),
        ]);
    }
    
    /**
     * @Route("/match/recherche", name="recherche_reclamation")
     */
    public function rechercheReclamation(Request $request,MatchsRepository $matchsRepository): Response
    {
        $recherche = $request->get("valeur-recherche");
        $recs = $matchsRepository->findStartingWith($recherche);

        $recsJson = [];
        $recsJsonn = [];
        $i = 0;
        foreach ($recs as $rec) {
         //   $recsJson[$i]["dateMatch"] = $rec->getDateMatch();
            $recsJsonn[$i]["equipe1"] = $rec->getId();
           
            $match = $this->getDoctrine()->getRepository(Matchs::class)->findBy(['equipe1' => $recsJsonn[$i]["equipe1"]]);
            foreach ($match as $m){
            $recsJson[$i]["id"] = $m->getId();
            $recsJson[$i]["date"] = $m->getDateMatch()->format('Y-m-d H:i:s');
            $recsJson[$i]["equipe1"] = $m->getEquipe1()->getNom();
            $recsJson[$i]["equipe2"] = $m->getEquipe2()->getNom();
            $recsJson[$i]["refMatch"] = $m->getRefMatch();
            $recsJson[$i]["scoreA"] = $m->getScoreA();
            $recsJson[$i]["scoreB"] = $m->getScoreB();            
            // $recsJson[$i]["message"] = $rec->getMessage();
            // $recsJson[$i]["statut"] = $rec->getStatut();

            // if ($rec->getReponse()){
            //     $recsJson[$i]["id"] = $rec->getReponse()->getId();

            //     $recsJson[$i]["hasRep"] = $rec->getReponse();
            // }

            $i++;
        }
        }
       // dump( $recsJson);die;
        return new Response(json_encode($recsJson));
    }

    /**
     * @Route("/matchs/new", name="matchs_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $match = new Matchs();
        $form = $this->createForm(MatchsType::class, $match);
        $form->handleRequest($request);
         
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($match);
            $entityManager->flush();

            return $this->redirectToRoute('matchs_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('matchs/new.html.twig', [
            'match' => $match,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/matchs", name="matchs_show")
     */
    public function show(Matchs $match): Response
    {
        return $this->render('matchs/show.html.twig', [
            'match' => $match,
        ]);
    }

    /**
     * @Route("/matchs/{id}/edit", name="matchs_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Matchs $match, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MatchsType::class, $match);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('matchs_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('matchs/edit.html.twig', [
            'match' => $match,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/matchs/{id}", name="matchs_delete", methods={"POST"})
     */
    public function delete(Request $request, Matchs $match, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$match->getId(), $request->request->get('_token'))) {
            $entityManager->remove($match);
            $entityManager->flush();
        }

        return $this->redirectToRoute('matchs_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/matchs/calenderiermatchs", name="calenderiermatchs")
     */
    public function calenderiermatchs(CalendarRepository $calendar)
    {
        $matchs = $calendar->findAll();

        $rdvs = [];

        foreach($matchs as $match){
            $rdvs[] = [
                'id' => $match->getId(),
                'start' => $match->getStart()->format('Y-m-d H:i:s'),
                'end' => $match->getEnd()->format('Y-m-d H:i:s'),
                'title' => $match->getTitle(),
                'description' => $match->getDescription(),
                'backgroundColor' => $match->getBackgroundColor(),
                'borderColor' => $match->getBorderColor(),
                'textColor' => $match->getTextColor(),
                'allDay' => $match->getAllDay(),
            ];
        }

        $data = json_encode($rdvs);

        return $this->render('matchs/index2.html.twig', compact('data'));
    }

}
