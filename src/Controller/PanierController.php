<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\ProduitPanier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class PanierController extends AbstractController
{
    /**
     * @Route("/panier", name="panier")
     */
    public function index(Security $sec): Response
    {
        //Geree la panier de user actuel
        $user= $sec->getUser();
        $panier=$this->getDoctrine()->getRepository(Panier::class)->findBy(['user'=>$user]);
        $pp= $this->getDoctrine()->getRepository(ProduitPanier::class)->findBy(['panier'=>$panier]);

        return $this->render('panier/index.html.twig', [
            'controller_name' => 'PanierController',
            'user'=>$user,
            'panier'=>$panier[0],
            'pps'=>$pp
        ]);
    }

    /**
     * @Route ("/dpp/{id}",name="dpp")
     */
    public function delete($id)
    {
        $repository=$this->getDoctrine()->getRepository(ProduitPanier::class);
        $pp=$repository->find($id);

        $em=$this->getDoctrine()->getManager();
        if($pp->getContiter()==1){
            $em->remove($pp);
        }else{
            $pp->setContiter($pp->getContiter()-1);
            $em->persist($pp);
        }

        $em->flush();
        return $this->redirectToRoute('affichepanier');
    }

    /**
     * @Route("/affichepanier", name="affichepanier")
     */
    public function affichepanier(Security $sec): Response
    {
        //affiche panier
        $user= $sec->getUser();
        $panier=$this->getDoctrine()->getRepository(Panier::class)->findBy(['user'=>$user]);
        $pp= $this->getDoctrine()->getRepository(ProduitPanier::class)->findBy(['panier'=>$panier]);

        return $this->render('panier/panier.html.twig', [
            'controller_name' => 'PanierController',
            'user'=>$user,
            'panier'=>$panier[0],
            'pps'=>$pp
        ]);
    }
    /**
     * @Route ("/checkout/{total}",name="checkout")
     */
    public function checkout($total){
        return $this->render('panier/payment.html.twig',['total'=>$total]);
    }

    /**
     * @Route ("/viderPanier", name="viderPanier")
     * @param Security $sec
     */
    public function vider(Security $sec)
    {
        $user= $sec->getUser();
        $panier=$this->getDoctrine()->getRepository(Panier::class)->findBy(['user'=>$user]);
        $pps=$this->getDoctrine()->getRepository(ProduitPanier::class)->findBy(['panier'=>$panier[0]]);
        foreach ($pps as $pp){
            $em=$this->getDoctrine()->getManager();
                $em->remove($pp);
            $em->flush();
        }
        return $this->redirectToRoute('app_logout');
    }

}
