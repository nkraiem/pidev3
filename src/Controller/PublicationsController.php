<?php

namespace App\Controller;

use App\Entity\Publications;
use App\Form\PublicationsType;
use App\Repository\PublicationsRepository;
use App\Repository\LikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Like;

/**
 * @Route("/publications")
 */
class PublicationsController extends AbstractController
{
    /**
     * @Route("/", name="publications_index", methods={"GET"})
     */
    public function index(PublicationsRepository $publicationsRepository): Response
    {
        return $this->render('publications/index.html.twig', [
            'publications' => $publicationsRepository->findAll(),
        ]);
    }
    /**
     * @Route("/Front", name="Front_publications_index", methods={"GET"})
     */
    public function Frontindex(PublicationsRepository $publicationsRepository): Response
    {

        return $this->render('publications/FrontIndex.html.twig', [
            'publications' => $publicationsRepository->findAll(),
            
        ]);
    }

    /**
     * @Route("/new", name="publications_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $publication = new Publications();
        $form = $this->createForm(PublicationsType::class, $publication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $publication->getImagePub();
            $filename= md5(uniqid()).'.'.$file->guessExtension();
            $file->move($this->getParameter('upload_directory'),$filename);
            $publication->setImagePub($filename);
            $entityManager->persist($publication);
            $entityManager->flush();

            return $this->redirectToRoute('publications_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('publications/new.html.twig', [
            'publication' => $publication,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/Front/{id}/like",name="likepub1",methods={"GET", "POST"})
     */

    public function like(EntityManagerInterface $entityManager1,EntityManagerInterface $entityManager,LikeRepository $likerepo,PublicationsRepository $publicationsRepository,$id):Response
    {
        $like = new Like();
        $pub=$this->getDoctrine()->getRepository(Publications::class)->find($id);
        $like->setPublication($pub);
        $like->setRate(1);
        $this->getDoctrine()->getManager()->persist($like);
        //$entityManager1->persist($like);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('Front_publications_show',['id'=>$id]);

    }

/**
     * @Route("/Front/{id}/dislike",name="dislike",methods={"GET", "POST"})
     */

    public function dislike($id,Publications $publication,EntityManagerInterface $entityManager1,EntityManagerInterface $entityManager,LikeRepository $likerepo,PublicationsRepository $publicationsRepository):Response
    {
        $like = new Like();
        $pub=$this->getDoctrine()->getRepository(Publications::class)->find($id);
        $like->setPublication($pub);
        $like->setRate(0);
        $this->getDoctrine()->getManager()->persist($like);
        //$entityManager1->persist($like);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('Front_publications_show',['id'=>$id]);

    }


    /**
     * @Route("/stats",name="stats",methods={"GET"})
     */
    public function statistique(PublicationsRepository $repo)
    {
        
        $new[]= $repo->newBlog();
        $old[] = $repo->oldBlog();

         return $this->render('publications/stat.html.twig',[
            'new' => json_encode($new),
            'old' => json_encode($old)            
         
          ] );
    }









    /**
     * @Route("/{id}", name="publications_show", methods={"GET"})
     */
    public function show(Publications $publication): Response
    {
        return $this->render('publications/show.html.twig', [
            'publication' => $publication,
        ]);
    }

     /**
     * @Route("/Front/{id}", name="Front_publications_show", methods={"GET"})
     */
    public function Frontshow(Publications $publication): Response
    {
        return $this->render('publications/FrontShow.html.twig', [
            'publication' => $publication,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="publications_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Publications $publication, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PublicationsType::class, $publication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $publication->getImagePub();
            $filename= md5(uniqid()).'.'.$file->guessExtension();
            $file->move($this->getParameter('upload_directory'),$filename);
            $publication->setImagePub($filename);
            $entityManager->persist($publication);
            $entityManager->flush();

            return $this->redirectToRoute('publications_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('publications/edit.html.twig', [
            'publication' => $publication,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="publications_delete", methods={"POST"})
     */
    public function delete(Request $request, Publications $publication, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$publication->getId(), $request->request->get('_token'))) {
            $entityManager->remove($publication);
            $entityManager->flush();
        }

        return $this->redirectToRoute('publications_index', [], Response::HTTP_SEE_OTHER);
    }
}
