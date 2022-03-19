<?php

namespace App\Controller;

use App\Entity\Joueurs;
use App\Form\JoueursType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Routing\Annotation\Route;

class JoueurController extends AbstractController
{
    /**
     * @Route("/joueurs", name="joueurs")
     */
    public function afficher()
    {
        $joueur = $this->getDoctrine()->getRepository(Joueurs::class)->findAll();

        return $this->render('joueur/back/listj.html.twig', [
            'joueurs' => $joueur]);
    }

    /**
     * @Route("/front/joueurs", name="front/joueurs")
     */
    public function affichage()
    {
        $joueur = $this->getDoctrine()->getRepository(Joueurs::class)->findAll();

        return $this->render('joueur/front/affjoueur.html.twig', [
            'joueurs' => $joueur]);
    }
    /**
     * @Route("/supprimerjoueur/{idjoueur}", name="supprimerjoueur")
     */
    public
    function supprimerJoueurs($idjoueur): RedirectResponse
    {
        $joueur = $this->getDoctrine()->getRepository(Joueurs::class)->find($idjoueur);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($joueur);
        $entityManager->flush();

        return $this->redirectToRoute('joueurs');
    }

    /**
     * @Route("/ajoutjoueurs", name="ajoutjoueurs")
     */
    public function ajout(Request $request): Response
    {
        $joueurs = new Joueurs();
        $form = $this->createForm(JoueursType::class,$joueurs)->add('submit', SubmitType::class)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

          $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($joueurs);
            $entityManager->flush();

            return $this->redirectToRoute('joueurs');
        }

        return $this->render('joueur/back/ajoutjoueurs.html.twig', [
            'joueurs'=>$joueurs,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/modifjoueurs/{id_j}", name="modifjoueurs")
     */
    public function modifier(Request $request, $id_j): Response
    {  $joueur = $this->getDoctrine()->getRepository(Joueurs::class)->find($id_j);
        if ($request->getMethod() == 'POST') {

            $joueur->setNom($request->get('nom'));
            $joueur->setPrenom($request->get('prenom'));
            $joueur->setEmail($request->get('email'));
            $joueur->setNumero($request->get('numero'));
            $joueur->setNbrPartieJouer($request->get('nbrpartiejouer'));


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($joueur);
            $entityManager->flush();
            return $this->redirectToRoute('joueurs'
            );
        }
        return $this->render('joueur/back/modifjoueur.html.twig'
            ,[
            'joueurs' => $joueur
        ]);
    }

}




