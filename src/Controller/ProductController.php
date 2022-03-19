<?php

namespace App\Controller;
use Knp\Component\Pager\PaginatorInterface;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    
    /**
     * @Route("/product", name="product")
     * @param ProduitRepository $produitRepository

     */
    public function index(ProduitRepository $produitRepository,PaginatorInterface $paginator,Request $request): Response
    {

        return $this->render('produit/showc.html.twig', [
            $produits = $produitRepository->findAll(),
            'produits' => $paginator->paginate($produits,
                $request->query->getInt('page', 1), 3)
        ]);
    }

    /**
     * @Route("/message", name="message")
     */
    function messageAction(Request $request)
    {
        DriverManager::loadDriver(\BotMan\Drivers\Web\WebDriver::class);

        // Configuration for the BotMan WebDriver
        $config = [];

        // Create BotMan instance
        $botman = BotManFactory::create($config);

        // Give the bot some things to listen for.
        $botman->hears('(hello|hi|hey)', function (BotMan $bot) {
            $bot->reply('Hello!');
        });
        $botman->hears('how can i buy', function (BotMan $bot) {
            $bot->reply('you can add your wishlist in cart!');
        });
        $botman->hears('where is the cheapest product', function (BotMan $bot) {
            $bot->reply('you can search in the catalogue!');
        });
        // Set a fallback
        $botman->fallback(function (BotMan $bot) {
            $bot->reply('Sorry, I did not understand.');
        });

        // Start listening
        $botman->listen();

        return new Response();
    }



    /**
     * @Route("/chatframe", name="chatframe")
     */
    public function chatframeAction(Request $request)
    {
        return $this->render('produit/chat_frame.html.twig');
    }

}
