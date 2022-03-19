<?php

namespace App\Controller;

use App\Entity\Historique;
use App\Form\GamerType;
use App\Form\HistoriqueType;
use App\Form\SignType;
use App\Repository\GamerRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Gamer;
use Symfony\Component\HttpFoundation\Request;
use Vich\UploaderBundle\Form\Type\VichImageType;

class GamerController extends AbstractController
{
    /**
     * @Route("/gamer", name="gamer")
     */
    public function index(): Response
    {
        return $this->render('gamer/index.html.twig', [
            'controller_name' => 'GamerController',
        ]);
    }
    /**
     * @param GamerRepository $repository
     * @return Response
     * @Route ("/affichegamer",name="affichegamer")
     */
    public function affiche(GamerRepository $repository)
    {
        //$repository=$this->getDoctrine()->getRepository(Classroom::class);
        $tabgamer=$repository->findAll();
        return $this->render('gamer/affichegamer.html.twig',[
            'tab'=>$tabgamer
        ]);
    }
    /**
     * @Route ("/addgamer" , name="addgamer")
     */

    public function addgamer(Request $request)
    {

        $gamer = new Gamer();
        $form = $this->createForm(GamerType::class, $gamer);
        $form->add('imageFile',VichImageType::class);
        $form->add('Ajouter',SubmitType::class);
        $form->handleRequest($request);
        if (($form->isSubmitted())&& ($form->isValid())) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($gamer);
            $em->flush();
            return $this->redirectToRoute('affichegamer');
        }
        return $this->render("gamer/gamer.html.twig",array('form'=>$form->createView()));

    }
    /**
     * @Route ("/delete/{id}",name="delete")
     */
    public function delete($id,GamerRepository $repository)
    {

        $Gamer=$repository->find($id);
        var_dump($Gamer);

        $em=$this->getDoctrine()->getManager();
        $em->remove($Gamer);
        $em->flush();
        //return $this->redirectToRoute('affichegamer');
    }
    /**
     * @Route ("/updateg/{id}" , name="updateg")
     */
    public function update($id,GamerRepository $repository ,Request $request)
    {
        $Gamer=$repository->find($id);
        $form=$this->createForm(GamerType::class, $gamer);
        $form->add('imageFile',VichImageType::class);
        $form->add('update',SubmitType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em=$this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('affichegamer');
        }
        return $this->render('gamer/update.html.twig',['f'=>$form->createView()]);
    }


}
