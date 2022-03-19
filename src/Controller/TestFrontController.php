<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestFrontController extends AbstractController
{
    /**
     * @Route("/front", name="front")
     */
    public function index(): Response
    {
        return $this->render('base-front.html.twig');
    }
    /**
     * @Route("/frontBlog", name="front")
     */
    public function blog(): Response
    {
        return $this->render('blog.html.twig');
    }
    /**
     * @return Response
     * @Route ("/test", name="test")
     */
    public function TEST()
    {
        return $this->redirectToRoute('backindex',['saif'=>'4']);
    }
}
