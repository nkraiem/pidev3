<?php

namespace App\Controller;

use Knp\Component\Pager\PaginatorInterface;

use Symfony\Component\HttpFoundation\Request;
use App\Entity\Matchs;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/index", name="index")
     */
    public function index(): Response
    {
        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }

    /**
     * @Route("/results", name="fresults")
     */
    public function results(): Response
    {
        return $this->render('index/results.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }
    
    /**
     * @Route("/upcoming", name="fupcoming")
     */
    public function upcoming(Request $request,PaginatorInterface $paginator): Response
    {      
        $matchs = $this->getDoctrine()->getRepository(Matchs::class)->findAll();
        return $this->render('index/upcoming.html.twig', [
            'totalMatchs' => count($matchs),
            'matchs' => $paginator->paginate($matchs,
                $request->query->getInt('page', 1), 3)
            ]);
            
    }

    /**
     * @Route("/blog", name="fblog")
     */
    public function blog(): Response
    {
        return $this->render('index/blog.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }
}
