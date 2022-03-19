<?php

namespace App\Controller;


use App\Entity\Equipes;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\EquipesRepository;
use Webmozart\Assert\Assert;


class EquipeController extends AbstractController
{
    /**
     * @Route("/equipes", name="equipes")
     */
    public function afficher()
    {
        $equipe = $this->getDoctrine()->getRepository(Equipes::class)->findAll();

        return $this->render('equipe/back/equipe.html.twig', [
            'equipes' => $equipe]);
    }


    /**
     * @Route("/front/equipes", name="front/equipes")
     */
    public function affichage(Request $request,PaginatorInterface $paginator)
    {
        $equipe = $this->getDoctrine()->getRepository(Equipes::class)->findAll();

        return $this->render('equipe/front/affequipe.html.twig', [
            'totalEquipes' => count($equipe),
            'equipes' => $paginator->paginate($equipe,
                $request->query->getInt('page', 1), 3)
            ]);
    }

    /**
     * @Route("/supprimerequipe/{idequipe}", name="supprimerequipe")
     */
    public
    function supprimerEquipes($idequipe): RedirectResponse
    {
        $equipe = $this->getDoctrine()->getRepository(Equipes::class)->find($idequipe);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($equipe);
        $entityManager->flush();

        return $this->redirectToRoute('equipes');
    }

    /**
     * @Route("/ajoutequipes", name="ajoutequipes")
     */
    public function ajout(Request $request): Response
    {
        if ($request->getMethod() == 'POST') {
            $equipe = new Equipes();
            $equipe->setNom($request->get('nom'));
            if ($request->get('suspension')) {
                $equipe->setSuspension($request->get('suspension'));
            }
            else {
                $equipe->setSuspension(false);  
            }

            $equipe->setNbrVic($request->get('nbrvic'));
            $equipe->setNbrPer($request->get('nbrper'));
            $equipe->setNbrNull($request->get('nbrnull'));


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($equipe);
            $entityManager->flush();
            return $this->redirectToRoute('equipes'
            );
        }
        return $this->render('equipe/back/ajoutequipes.html.twig'
        );
    }

    /**
     * @Route("/modifequipes/{id_eq}", name="modifequipes")
     */
    public function modifier(Request $request, $id_eq): Response
    {
        $equipe = $this->getDoctrine()->getRepository(Equipes::class)->find($id_eq);

        if ($request->getMethod() == 'POST') {
            $equipe->setNom($request->get('nom'));
            $equipe->setNbrVic($request->get('nbrvic'));
            $equipe->setNbrPer($request->get('nbrper'));
            $equipe->setNbrNull($request->get('nbrnull'));


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($equipe);
            $entityManager->flush();
            return $this->redirectToRoute('equipes'
            );
        }
        return $this->render('equipe/back/modifequipes.html.twig'
            , [
                'equipes' => $equipe
            ]);
    }

    /**
     * @Route("front/equipe/recherche", name="recherche_equipe")
     * @throws ExceptionInterface
     */
    public function rechercheEquipe(Request $request, NormalizerInterface $normalizer): Response
    {
        $recherche = $request->get("valeur-recherche");
        $equipes = $this->getDoctrine()->getRepository(Equipes::class)->findStartingWith($recherche);

        $equipesJson = [];
        $i = 0;
        foreach ($equipes as $equipe){
            $equipesJson[$i]["nom"] = $equipe->getNom();
            $equipesJson[$i]["nbr_vic"] = $equipe->getNbrVic();
            $equipesJson[$i]["nbr_per"] = $equipe->getNbrPer();
            $equipesJson[$i]["nbr_null"] = $equipe->getNbrNull();
            $i++;
        }

        return new Response(json_encode($equipesJson));
    }
    /**
     * @Route("equipes/{id}/suspendre", name="suspendre_equipe")
     */

    public function suspendreEquipe($id)
    {
        $equipe = $this->getDoctrine()->getRepository(Equipes::class)->find($id);
        $equipe->setSuspension(0);
        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($equipe);
        $entityManager->flush();
        return $this->redirectToRoute('equipes');
    }

    /**
     * @Route("equipes/{id}/activer", name="actif_equipe")
     */
    public function activerEquipe($id)
    {
        $equipe = $this->getDoctrine()->getRepository(Equipes::class)->find($id);
        $equipe->setSuspension(1);
        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($equipe);
        $entityManager->flush();
        return $this->redirectToRoute('equipes');
    }

    
    /**
     * @Route("equipes/stats",name="statsequipe",methods={"GET"})
     */
    public function statistique(EquipesRepository $repo)
    {
        
        $vic[]= $repo->victoire();
        $per[] = $repo->defaite();
        $nul[] = $repo->nul();


         return $this->render('equipe/back/stat.html.twig',[
            'vic' => json_encode($vic),
            'per' => json_encode($per),
            'nul' => json_encode($nul)            
         
          ] );
    }

}

