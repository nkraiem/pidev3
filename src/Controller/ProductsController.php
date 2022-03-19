<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\Product;
use App\Entity\ProduitPanier;
use App\Form\ProductType;
use App\Form\ProduitPanierType;
use App\Repository\ProductRepository;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;


class ProductsController extends AbstractController
{
    /**
     * @Route("/products", name="products")
     */
    public function index(): Response
    {
        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
        ]);
    }

    /**
 * @param ProductRepository $repository
 * @return Response
 * @Route ("/afficheProduct",name="afficheProduct")
 */
    public function affiche(ProductRepository $repository, Request $request, Security $sec)
    {
        //$repository=$this->getDoctrine()->getRepository(Classroom::class);

        $tabProduct=$repository->findAll();

        return $this->render('product/afficheprod.html.twig',[
            'tab'=>$tabProduct        ]);
    }

    /**
     * @Route ("/addToPanier/{idProduit}", name="addToPanier")
     */
    public function addToPanier( Request $request, Security $sec, $idProduit){
        $user= $sec->getUser();
        $panier=$this->getDoctrine()->getRepository(Panier::class)->findOneBy(['user'=>$user]);
        $produit=$this->getDoctrine()->getRepository(Product::class)->find($idProduit);

        //Ajouter au panier
        $produitPanier= $this->getDoctrine()->getRepository(ProduitPanier::class)->findOneBy(['panier'=>$panier, 'produit'=>$produit]);
        if(!isset($produitPanier)) {
            $produitPanier= new ProduitPanier();
            $produitPanier->setPanier($panier);
            $produitPanier->setProduit($produit);
            $produitPanier->setContiter(1);
        }else {
            $produitPanier->setContiter($produitPanier->getContiter()+1);
        }

            $em=$this->getDoctrine()->getManager();
            $em->persist($produitPanier);
            $em->flush();
            return $this->redirectToRoute('afficheStore');

    }


    /**
     * @Route ("/addproduct", name="addproduct")
     */
    public function coproduct(Request $request): Response
    {
        $product=new Product();
        $formproduct=$this->createForm(ProductType::class ,$product );
        $formproduct->add('Ajouter',SubmitType::class);
        $formproduct->handleRequest($request);
        if($formproduct->isSubmitted()&&$formproduct->isValid())
        {
            $em=$this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();
            return $this->redirectToRoute('afficheProduct');
        }
        return $this->render('product/addprod.html.twig',['form'=>$formproduct->createView()]);
    }
    /**
     * @Route ("/d/{id}",name="d")
     */
    public function delete($id)
    {
        $repository=$this->getDoctrine()->getRepository(Product::class);
        $product=$repository->find($id);
        $em=$this->getDoctrine()->getManager();
        $em->remove($product);
        $em->flush();
        return $this->redirectToRoute('afficheProduct');
    }
    /**
     * @Route ("/update/{id}" , name="update")
     */
    public function update($id,ProductRepository $repository ,Request $request)
    {
        $product=$repository->find($id);
        $form=$this->createForm(ProductType::class, $product);
        $form->add('update',SubmitType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em=$this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('afficheProduct');
        }
        return $this->render('product/update.html.twig',['f'=>$form->createView()]);
    }
    /**
     * @param ProductRepository $repository
     * @return Response
     * @Route ("/afficheStore",name="afficheStore")
     */
    public function afficheStore(ProductRepository $repository, Request $request, Security $sec)
    {


        $tabProduct=$repository->findAll();

        return $this->render('product/ryhabstor.html.twig',[
            'tab'=>$tabProduct        ]);
    }



}
