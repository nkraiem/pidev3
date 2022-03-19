<?php


namespace App\Controller;



use App\Entity\Matchs;
use App\Repository\MatchsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FavorisController extends AbstractController
{
    /**
     * @Route("/favoris", name="favoris")
     */
    public function index(SessionInterface $session, MatchsRepository  $matchRepository)
    {
        $favoris = $session->get("favoris", []);

        // On "fabrique" les données
        $dataFavoris = [];

        foreach($favoris as $id => $quantite){

            $match = $matchRepository->find($id);
            $dataFavoris[] = [
                "match" => $match,
                "quantite" => $quantite

            ];

        }
        return $this->render('favoris/favoris.html.twig', compact("dataFavoris"));
    }

    /**
     * @Route("/add/{id}", name="add")
     */
    public function add(Matchs $matchs, SessionInterface $session)
    {
        $favoris = $session->get("favoris", []);
        $id = $matchs->getId();

        if(!empty($favoris[$id])){
            $favoris[$id]++;
        }else{
            $favoris[$id] = 1;
        }

        // On sauvegarde dans la session
        $session->set("favoris", $favoris);

        return $this->redirectToRoute("favoris");
    }

    /**
     * @Route("/remove/{id}", name="supp")
     */
    public function remove(Matchs $matchs, SessionInterface $session)
    {
        $favoris = $session->get("favoris", []);
        $id = $matchs->getId();

        if(!empty($favoris[$id])){
            if($favoris[$id] > 1){
                $favoris[$id]--;
            }else{
                unset($favoris[$id]);
            }
        }

        // On sauvegarde dans la session
        $session->set("favoris", $favoris);
        return $this->redirectToRoute("favoris");
    }


    /**
     * @Route("/delete", name="delete_all")
     */
    public function deleteAll(SessionInterface $session)
    {
        $session->remove("favoris");

        return $this->redirectToRoute("favoris");
    }
}