<?php

namespace App\Controller;


use App\Entity\Commantaires;
use App\Entity\Equipes;
use App\Entity\Joueurs;
use App\Entity\Like;
use App\Entity\Matchs;
use App\Entity\Publications;
use App\Entity\Tournois;
use App\Entity\User;
use App\Repository\CommantairesRepository;
use App\Repository\EquipesRepository;
use App\Repository\JoueursRepository;
use App\Repository\LikeRepository;
use App\Repository\MatchsRepository;
use App\Repository\PublicationsRepository;
use App\Repository\TournoisRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use DoctrineExtensions\Query\Mysql\Date;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Request;

class MobileController extends AbstractController
{
    /**
     * @Route("/mobile/login_mobile/{email}/{password}", name="login_mobile")
     */
    public function login_mobile($email,$password,UserPasswordEncoderInterface $encoder,MatchsRepository  $matchsRepository)
    {
        $em = $this->getDoctrine()->getManager();

        $user=   $this->getDoctrine()->getManager()->getRepository(User::class)->findOneBy(array('email' => $email));
        $prd = array();
        if($user) {
            $passwordValid = $encoder->isPasswordValid($user, $password);
if($passwordValid) {
    $prd = array(
        'id' => $user->getId(),
        'name' => $user->getName(),
        'password' => $user->getPassword(),
        'email' => $user->getEmail(),
        'role' => $user->getRoles(),
        'image' => $user->getImage(),
        'lastname' => $user->getLastName()

    );

}
        }


        //firas chargement des matchs
       $matchs= $matchsRepository->findAll();
        foreach ($matchs as $match){
            $d=new \DateTime();
            $heure= strtotime( $match->getDateMatch()->format("d-m-Y H:i"))- strtotime ($d->format("d-m-Y H:i"));
            if(( $heure /(60*60))<-2 && $match->getScoreA()<0) {

                $score1 = rand(0, 4);
                $score2 = rand(0, 4);
                $match->setScoreA($score1);
                $match->setScoreB($score2);
                if($score1>$score2){
                    $match->getEquipe1()->setNbrVic($match->getEquipe1()->getNbrVic()+1);
                    $match->getEquipe2()->setNbrPer($match->getEquipe2()->getNbrPer()+1);
                }else if ($score1<$score2){
                    $match->getEquipe2()->setNbrVic($match->getEquipe2()->getNbrVic()+1);
                    $match->getEquipe1()->setNbrPer($match->getEquipe1()->getNbrPer()+1);
                }else{
                    $match->getEquipe2()->setNbrNull($match->getEquipe2()->getNbrNull()+1);
                    $match->getEquipe1()->setNbrNull($match->getEquipe1()->getNbrNull()+1);
                }
                $this->getDoctrine()->getManager()->flush();
            }
        }

        return new JsonResponse($prd);

    }

    /**
     * @Route("/mobile/inscrireMobile", name="inscrireMobile")
     */
    public function inscrireMobile( UserPasswordEncoderInterface $userPasswordEncoder,\Swift_Mailer $mailer,Request $request){

        $name = $request->query->get('name');
        $lastName = $request->query->get('lastName');
        $email = $request->query->get('email');
        $password = $request->query->get('password');
        $pathimage = $request->query->get('pathimage');


        $user =new User();
        $user->setName($name);
        $user->setLastName($lastName);
        $user->setEmail($email);





        $destinationfile=md5(uniqid()).".png";
        $destination=$this->getParameter('images_directory').'/products/'. $destinationfile;

        copy($pathimage, $destination);

        $user->setImage($destinationfile);

        $user->setPassword($password);
        $user->setPassword(
            $userPasswordEncoder->encodePassword(
                $user,
                $password
            )
        );

        $user->setActivationToken(md5(uniqid()));

        $em=$this->getDoctrine()->getManager();


        try {
            $em->persist($user);
            $em->flush();

            $message=(new \Swift_Message('Activation de votre compte'))
                ->setFrom('eslord00gaming@gmail.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'emails/activation.html.twig',['token'=>$user->getActivationToken()]
                    ),'text/html'
                );
            $mailer->send($message);





        } catch(\Exception $ex)
        {
            die($ex);
            $data = [
                'title' => 'validation error',
                'message' => 'Some thing went Wrong',
                'errors' => $ex->getMessage()
            ];
            $response = new JsonResponse($data,400);
            return $response;
        }

        return $this->json(array('title'=>'successful','message'=> "utilisateur ajouté avec succès"),200);


    }

    /**
     * @Route("/mobile/getAllUsers", name="getAllUsers")
     */
    public  function getAllUsers(){
        ;
        $em = $this->getDoctrine()->getManager();

        $users=   $this->getDoctrine()->getManager()->getRepository(User::class)->findAll();
        $jsonData=array();
        $prd=array();
        $i=0;
        foreach ($users as $user){

            $prd = array(
                'id' => $user->getId(),
                'name' => $user->getName(),
                'password' => $user->getPassword(),
                'email' => $user->getEmail(),
                'role' => $user->getRoles()

            );
            $jsonData[$i++] = $prd;
        }


        return new JsonResponse($jsonData);

    }
    /**
     * @Route("/mobile/getUserByEmail/{email}", name="getUserByEmail")
     */
    public function getUserByEmail($email)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->getDoctrine()->getManager()->getRepository(User::class)->findOneBy(array('email' => $email));
        $prd = array();
        if ($user) {
            $prd = array(
                'id' => $user->getId(),
                'name' => $user->getName(),
                'lastname' => $user->getLastName(),
                'email' => $user->getEmail(),
                'role' => $user->getRoles(),
                'image' => $user->getImage()

            );
        }

        return new JsonResponse($prd);
    }

    /**
     * @Route("/mobile/removeUser/{email}", name="removeUser")
     */
    public function removeUser($email)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->getDoctrine()->getManager()->getRepository(User::class)->findOneBy(array('email' => $email));

        $em->remove($user);
        $em->flush();
        return $this->json(array('title'=>'successful','message'=> "utilisateur supprimé avec succès"),200);
    }


    /**
* @Route("/mobile/updateUser", name="updateUser")
*/
    public function updateUser( UserPasswordEncoderInterface $userPasswordEncoder,\Swift_Mailer $mailer,Request $request){

        $name = $request->query->get('name');
        $lastName = $request->query->get('lastName');
        $email = $request->query->get('email');
        $password = $request->query->get('password');
        $pathimage = $request->query->get('pathimage');


        $user =$em = $this->getDoctrine()->getManager();

        $user = $this->getDoctrine()->getManager()->getRepository(User::class)->findOneBy(array('email' => $email));

        $user->setName($name);
        $user->setLastName($lastName);



        $destinationfile=md5(uniqid()).".png";
        $destination=$this->getParameter('images_directory').'/products/'. $destinationfile;

        copy($pathimage, $destination);

        $user->setImage($destinationfile);

        $user->setPassword($password);
        $user->setPassword(
            $userPasswordEncoder->encodePassword(
                $user,
                $password
            )
        );



        $em=$this->getDoctrine()->getManager();


        try {
            $em->persist($user);
            $em->flush();

            $message=(new \Swift_Message('Changement de coordonéés'))
                ->setFrom('eslord00gaming@gmail.com')
                ->setTo($user->getEmail())
                ->setBody(
                   "Nous vous informons que vos cordonnées ont été changés avec succès"
                );
            $mailer->send($message);



        } catch(\Exception $ex)
        {
            die($ex);
            $data = [
                'title' => 'validation error',
                'message' => 'Some thing went Wrong',
                'errors' => $ex->getMessage()
            ];
            $response = new JsonResponse($data,400);
            return $response;
        }

        return $this->json(array('title'=>'successful','message'=> "utilisateur ajouté avec succès"),200);


    }


    /**
     *@Route("/mobile/sendpdf/{mail}", name="sendpdf")
     */
    public function sendpdf(UserRepository  $repository,\Swift_Mailer $mailer,$mail)
    {

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        $prod=$repository->findAll();




        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $html= $this->render("user/pdf.html.twig",['tour'=>$prod]);



        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        // Store PDF Binary Data
        $output = $dompdf->output();

        // In this case, we want to write the file in the public directory
        $publicDirectory = $this->getParameter('images_directory').'/products/';
        // e.g /var/www/project/public/mypdf.pdf
        $pdfFilepath =  $publicDirectory . '/Users.pdf';

        // Write file to the desired path
        file_put_contents($pdfFilepath, $output);


        $message=(new \Swift_Message('Liste Users'))
            ->setFrom('eslord00gaming@gmail.com')
            ->setTo($mail)
            ->setBody(
                "Bonjour, Voici la liste des utilisateurs , ci-joint. Cordialement,"
            )
            ->attach(\Swift_Attachment::fromPath($pdfFilepath));
        $mailer->send($message);
        return $this->json(array('title'=>'successful','message'=> "pdf envoyé"),200);

    }


    //*******Forum************//

    /**
     * @Route("/mobile/allpublication", name="allpublication")
     */
    public function allpublication(PublicationsRepository $publicationsRepository): Response
    {
       $pubs=$publicationsRepository->findAll();

        usort($pubs, function($a, $b) {  return count($a->getLikes()) <=> count($b->getLikes()); });
        $pubs=array_reverse($pubs,true);
        $jsonData=array();
        $prd=array();
        $i=0;
        foreach ($pubs as $pub){

            $prd = array(
                'autheur' => $pub->getAutheurPub(),
                'contenu' => $pub->getContenuPub(),
                'date' => $pub->getDatePub(),
                'id' => $pub->getId(),
                'titre' => $pub->getTitrePub(),
                'image' => $pub->getImagePub(),


            );
            $jsonData[$i++] = $prd;
        }


        return new JsonResponse($jsonData);
    }

    /**
     * @Route("/mobile/addpublication", name="addpublication")
     */
    public function addpublication( Request $request){

        $titre = $request->query->get('titre');
        $contenu = $request->query->get('contenu');
        $autheur = $request->query->get('autheur');
        $pathimage = $request->query->get('image');
        $datepub=new \DateTime();


        $pub =new Publications();
        $pub->setAutheurPub($autheur);
        $pub->setContenuPub($contenu);
        $pub->setDatePub($datepub);
        $pub->setTitrePub($titre);


        $destinationfile=md5(uniqid()).".png";
        $destination=$this->getParameter('upload_directory').'/'. $destinationfile;

        copy($pathimage, $destination);

        $pub->setImagePub($destinationfile);


        $em=$this->getDoctrine()->getManager();


        try {
            $em->persist($pub);
            $em->flush();
        } catch(\Exception $ex)
        {
            die($ex);
            $data = [
                'title' => 'validation error',
                'message' => 'Some thing went Wrong',
                'errors' => $ex->getMessage()
            ];
            $response = new JsonResponse($data,400);
            return $response;
        }



        return $this->json(array('title'=>'successful','message'=> "publication ajouté avec succès"),200);


    }


    /**
     * @Route("/mobile/editpublication", name="editpublication")
     */
    public function editpublication( Request $request,PublicationsRepository $publicationsRepository){

        $id=$request->query->get('id');
        $titre = $request->query->get('titre');
        $contenu = $request->query->get('contenu');
        $autheur = $request->query->get('autheur');
        $pathimage = $request->query->get('image');
        $datepub=new \DateTime();



        $pub =$publicationsRepository->find($id);
        $pub->setAutheurPub($autheur);
        $pub->setContenuPub($contenu);
        $pub->setDatePub($datepub);
        $pub->setTitrePub($titre);


        $destinationfile=md5(uniqid()).".png";
        $destination=$this->getParameter('upload_directory').'/'. $destinationfile;

        copy($pathimage, $destination);

        $pub->setImagePub($destinationfile);


        $em=$this->getDoctrine()->getManager();


        try {
            $em->persist($pub);
            $em->flush();
        } catch(\Exception $ex)
        {
            die($ex);
            $data = [
                'title' => 'validation error',
                'message' => 'Some thing went Wrong',
                'errors' => $ex->getMessage()
            ];
            $response = new JsonResponse($data,400);
            return $response;
        }

        return $this->json(array('title'=>'successful','message'=> "publication ajouté avec succès"),200);


    }


    /**
     * @Route("/mobile/addlike",name="addlike")
     */

    public function addlike(Request $request ):Response
    {
        $idpub=$request->query->get('pub');
        $rate = $request->query->get('rate');

        $like = new Like();
        $pub=$this->getDoctrine()->getRepository(Publications::class)->find($idpub);
        $like->setPublication($pub);
        $like->setRate($rate);
        $this->getDoctrine()->getManager()->persist($like);

        $this->getDoctrine()->getManager()->flush();
        return $this->json(array('title'=>'successful','message'=> "like/dislike ajouté avec succès"),200);



    }

    /**
     * @Route("/mobile/allLike", name="allLike")
     */
    public function allLike()
    {
        $em = $this->getDoctrine()->getManager();

        $likes=   $this->getDoctrine()->getManager()->getRepository(Like::class)->findAll();
        $jsonData=array();
        $prd=array();
        $i=0;
        foreach ($likes as $like){

            $prd = array(

                'rate' => $like->getRate(),

            );
            $jsonData[$i++] = $prd;
        }


        return new JsonResponse($jsonData);

    }



    /**
     * @Route("/mobile/countLikebypublication/{idpub}", name="countLikebypublication")
     */
    public function countLikebypublication($idpub)
    {
        $em = $this->getDoctrine()->getManager();
        $pub=$this->getDoctrine()->getRepository(Publications::class)->find($idpub);
        $like=   $this->getDoctrine()->getManager()->getRepository(Like::class)->findBy(array('publication' => $pub,'rate' =>1));
        $prd = array();


                $prd = array(
                    'nbrlike' => count($like),
                );
        return new JsonResponse($prd);

    }

    /**
     * @Route("/mobile/countDISLIKEbypublication/{idpub}", name="countDISLIKEbypublication")
     */
    public function countDISLIKEbypublication($idpub)
    {
        $em = $this->getDoctrine()->getManager();
        $pub=$this->getDoctrine()->getRepository(Publications::class)->find($idpub);
        $like=   $this->getDoctrine()->getManager()->getRepository(Like::class)->findBy(array('publication' => $pub,'rate' =>0));
        $prd = array();


        $prd = array(
            'nbrlike' => count($like),
        );
        return new JsonResponse($prd);

    }



    /**
     * @Route("/mobile/addCommentaire",name="addCommentaire")
     */

    public function addCommentaire(Request $request,\Swift_Mailer $mailer ):Response
    {
        $idpub=$request->query->get('pub');
        $contenu = $request->query->get('contenu');

        $pub=$this->getDoctrine()->getRepository(Publications::class)->find($idpub);

        $comm=new Commantaires();
        $comm->setContenu($contenu);
        $comm->setDateCommentaire(new \DateTime());
        $comm->setPublication($pub);

       $this->getDoctrine()->getManager()->persist($comm);

        $this->getDoctrine()->getManager()->flush();


        $message=(new \Swift_Message('Nouveau commentaire pour la publication: '.$pub->getTitrePub()))
            ->setFrom('eslord00gaming@gmail.com')
            ->setTo('mohamedrami.jammeli@esprit.tn')
            ->setBody(
                "Consultez votre forum pour le sujet ". $pub->getTitrePub(). 'un nouveau commentaire vient d\être publié'
            );
        $mailer->send($message);


        return $this->json(array('title'=>'successful','message'=> "like/dislike ajouté avec succès"),200);



    }

    /**
     * @Route("/mobile/allcommentsbypub/{idpub}", name="allcommentsbypub")
     */
    public function allcommentsbypub(CommantairesRepository $commantairesRepository,$idpub): Response
    {
        $publ=$this->getDoctrine()->getRepository(Publications::class)->find($idpub);
        $comms=$commantairesRepository->findBy(array("publication"=>$publ));

        usort($comms, function($a, $b) {  return strtotime($a->getDateCommentaire()->format('d-m-Y H:i:s')) <=> strtotime($b->getDateCommentaire()->format('d-m-Y H:i:s')); });
        $comms=array_reverse($comms,true);

        $jsonData=array();
        $prd=array();
        $i=0;
        foreach ($comms as $com){

            $prd = array(
                'contenu' => $com->getContenu(),
                'date' => $com->getDateCommentaire(),
                'idpub' => $com->getPublication()->getId(),
                'id' => $com->getId(),

            );
            $jsonData[$i++] = $prd;
        }


        return new JsonResponse($jsonData);
    }


    //**************************Equipe/joueur*********************/////////////

    /**
     * @Route("/mobile/allEquipes", name="allEquipes")
     */
    public function allEquipes(EquipesRepository $equipesRepository): Response
    {

        $equipes=$equipesRepository->findAll();



        $jsonData=array();
        $prd=array();
        $i=0;
        foreach ($equipes as $e){

            $prd = array(
                'id' => $e->getId(),
                'nom' => $e->getNom(),
                'nbr_vic' => $e->getNbrVic(),
                'nbr_per' => $e->getNbrPer(),
                'nbr_null' => $e->getNbrNull(),
                'suspension' => $e->getSuspension(),


            );
            $jsonData[$i++] = $prd;
        }


        return new JsonResponse($jsonData);
    }

    /**
     * @Route("/mobile/addEquipe",name="addEquipe")
     */

    public function addEquipe(Request $request ):Response
    {
        $nom=$request->query->get('nom');

        $equipe=new Equipes();

        $equipe->setNom($nom);
        $equipe->setNbrNull(0);
        $equipe->setNbrPer(0);
        $equipe->setNbrVic(0);
        $equipe->setSuspension(false);


        $this->getDoctrine()->getManager()->persist($equipe);

        $this->getDoctrine()->getManager()->flush();




        return $this->json(array('title'=>'successful','message'=> "Equipe ajoutée avec succès"),200);



    }

    /**
     * @Route("/mobile/editequipe",name="editequipe")
     */

    public function editequipe(Request $request,EquipesRepository $equipesRepository):Response
    {
        $nom=$request->query->get('nom');
        $id=$request->query->get('id');

        $equipe=$equipesRepository->find($id);

        $equipe->setNom($nom);



        $this->getDoctrine()->getManager()->persist($equipe);

        $this->getDoctrine()->getManager()->flush();




        return $this->json(array('title'=>'successful','message'=> "Equipe modifié avec succès"),200);



    }


    /**
     * @Route("/mobile/removeEquipe/{id}",name="removeEquipe")
     */

    public function removeEquipe(EquipesRepository $equipesRepository,$id):Response
    {


        $equipe=$equipesRepository->find($id);





        $this->getDoctrine()->getManager()->remove($equipe);

        $this->getDoctrine()->getManager()->flush();




        return $this->json(array('title'=>'successful','message'=> "Equipe supprimé avec succès"),200);



    }


    /**
     * @Route("/mobile/active_suspend",name="active_suspend")
     */

    public function active_suspend(Request $request,\Swift_Mailer $mailer,EquipesRepository $equipesRepository):Response
    {
        $suspension=$request->query->get('suspension');
        $id=$request->query->get('id');
        $mail=$request->query->get('mail');

        $equipe=$equipesRepository->find($id);

        if($suspension==0)
        $equipe->setSuspension(false);
        else {
            $equipe->setSuspension(true);

            $message=(new \Swift_Message('Une équipe suspendue'))
                ->setFrom('eslord00gaming@gmail.com')
                ->setTo($mail)
                ->setBody(
                    "Consultez vos equipes , une entre eux vient d etre suspendue"
                );
            $mailer->send($message);
            
            
            
            
            
       

        }


        $this->getDoctrine()->getManager()->persist($equipe);

        $this->getDoctrine()->getManager()->flush();




        return $this->json(array('title'=>'successful','message'=> "Equipe modifié avec succès"),200);



    }




    /**
     * @Route("/mobile/addJoueur",name="addJoueur")
     */

    public function addJoueur(Request $request ):Response
    {
        $nom=$request->query->get('nom');
        $prenom=$request->query->get('prenom');
        $numero=$request->query->get('numero');
        $mail=$request->query->get('mail');
        $equipeid=$request->query->get('equipeid');


        $equipe=$this->getDoctrine()->getRepository(Equipes::class)->find($equipeid);

        $j=new Joueurs();
        $j->setEmail($mail);
        $j->setEquipes($equipe);
        $j->setNom($nom);
        $j->setPrenom($prenom);
        $j->setNumero($numero);
        $j->setNbrPartieJouer(0);

        $this->getDoctrine()->getManager()->persist($j);

        $this->getDoctrine()->getManager()->flush();




        return $this->json(array('title'=>'successful','message'=> "Joueur ajouté avec succès"),200);



    }


    /**
     * @Route("/mobile/editJoueur",name="editJoueur")
     */

    public function editJoueur(Request $request ):Response
    {
        $nom=$request->query->get('nom');
        $prenom=$request->query->get('prenom');
        $numero=$request->query->get('numero');
        $mail=$request->query->get('mail');

        $id=$request->query->get('id');


        $j=$this->getDoctrine()->getRepository(Joueurs::class)->find($id);


        $j->setEmail($mail);

        $j->setNom($nom);
        $j->setPrenom($prenom);
        $j->setNumero($numero);


        $this->getDoctrine()->getManager()->persist($j);

        $this->getDoctrine()->getManager()->flush();




        return $this->json(array('title'=>'successful','message'=> "Joueur ajouté avec succès"),200);



    }


    /**
     * @Route("/mobile/removeJoueur/{id}",name="removeJoueur")
     */

    public function removeJoueur($id):Response
    {



        $joueur=$this->getDoctrine()->getRepository(Joueurs::class)->find($id);


        $this->getDoctrine()->getManager()->remove($joueur);

        $this->getDoctrine()->getManager()->flush();




        return $this->json(array('title'=>'successful','message'=> "Joueur ajouté avec succès"),200);



    }


    /**
     * @Route("/mobile/alljoueurbyequipe/{idequipe}", name="alljoueurbyequipe")
     */
    public function alljoueurbyequipe(JoueursRepository $joueursRepository,$idequipe): Response
    {
        $equipe=$this->getDoctrine()->getRepository(Equipes::class)->find($idequipe);
        $joueurs=$joueursRepository->findBy(array("equipes"=>$equipe));



        $jsonData=array();
        $prd=array();
        $i=0;
        foreach ($joueurs as $j){

            $prd = array(
                'id' => $j->getId(),
                'nom' => $j->getNom(),
                'prenom' => $j->getPrenom(),
                'mail' => $j->getEmail(),
                'numero' => $j->getNumero(),

            );
            $jsonData[$i++] = $prd;
        }


        return new JsonResponse($jsonData);
    }

    //*******************Tournoi/Match**********************************//
    /**
     * @Route("/mobile/addTournois",name="addTournois")
     */

    public function addTournois(Request $request ):Response
    {
        $nom=$request->query->get('nom');
        $datedebut=$request->query->get('datedebut');
        $datefin=$request->query->get('datefin');
        $prime=$request->query->get('prime');

        $datedebut=$datedebut." 00:00";
        $datefin=$datefin." 00:00";

        $tournoi=new Tournois();

        $date1 = \DateTime::createFromFormat('d/m/y H:i',$datedebut);
        $date2 = \DateTime::createFromFormat('d/m/y H:i',$datefin);



        $tournoi->setNom($nom);
        $tournoi->setDateDebut($date1);
        $tournoi->setDateFin($date2);
        $tournoi->setPrime($prime);



        $this->getDoctrine()->getManager()->persist($tournoi);

        $this->getDoctrine()->getManager()->flush();




        return $this->json(array('title'=>'successful','message'=> "Equipe ajoutée avec succès"),200);



    }


    /**
     * @Route("/mobile/allTournois", name="allTournois")
     */
    public function allTournois(TournoisRepository $tournoisRepository): Response
    {
        $tournois=$tournoisRepository->findAll();


        $jsonData=array();
        $prd=array();
        $i=0;
        foreach ($tournois as $t){

            $prd = array(
                'id' => $t->getId(),
                'nom' => $t->getNom(),
                'date' => $t->getDateDebut(),
                'date2' => $t->getDateFin(),
                'prime' => $t->getPrime(),


            );
            $jsonData[$i++] = $prd;
        }


        return new JsonResponse($jsonData);
    }


    /**
     * @Route("/mobile/edittournois",name="edittournois")
     */

    public function edittournois(Request $request,TournoisRepository $tournoisRepository):Response
    {
        $id=$request->query->get('id');
        $nom=$request->query->get('nom');
        $datedebut=$request->query->get('datedebut');
        $datefin=$request->query->get('datefin');
        $prime=$request->query->get('prime');

        $datedebut=$datedebut." 00:00";
        $datefin=$datefin." 00:00";



        $date1 = \DateTime::createFromFormat('d/m/y H:i',$datedebut);
        $date2 = \DateTime::createFromFormat('d/m/y H:i',$datefin);

        $tournoi= $tournoisRepository->find($id);

        $tournoi->setNom($nom);
        $tournoi->setDateDebut($date1);
        $tournoi->setDateFin($date2);
        $tournoi->setPrime($prime);



        $this->getDoctrine()->getManager()->persist($tournoi);

        $this->getDoctrine()->getManager()->flush();




        return $this->json(array('title'=>'successful','message'=> "Equipe ajoutée avec succès"),200);



    }


    /**
     * @Route("/mobile/removeTournois/{id}",name="removeTournois")
     */

    public function removeTournois(TournoisRepository $tournoisRepository,$id):Response
    {


        $t=$tournoisRepository->find($id);





        $this->getDoctrine()->getManager()->remove($t);

        $this->getDoctrine()->getManager()->flush();




        return $this->json(array('title'=>'successful','message'=> "Equipe supprimé avec succès"),200);



    }

    /**
     * @Route("/mobile/addMatch",name="addMatch")
     */

    public function addMatch(Request $request,TournoisRepository  $tournoisRepository,EquipesRepository  $equipesRepository):Response
    {
        $ref=$request->query->get('ref');
        $tournoiid=$request->query->get('tournoiid');
        $equip1=$request->query->get('equip1');
        $equip2=$request->query->get('equip2');
        $date=$request->query->get('date');


        $date=str_replace("%"," ",$date);
        $date1 = \DateTime::createFromFormat('d/m/y H:i',$date);

       $tournoi= $tournoisRepository->find($tournoiid);
       $equipe1=$equipesRepository->find($equip1);
       $equipe2=$equipesRepository->find($equip2);

$match=new Matchs();
$match->setTournoi($tournoi);
$match->setEquipe1($equipe1);
$match->setEquipe2($equipe2);
$match->setDateMatch($date1);
$match->setRefMatch($ref);
$match->setScoreA(-1);
$match->setScoreB(-1);





        $this->getDoctrine()->getManager()->persist($match);

        $this->getDoctrine()->getManager()->flush();




        return $this->json(array('title'=>'successful','message'=> "Equipe ajoutée avec succès"),200);



    }


    /**
     * @Route("/mobile/allMatchsbytournois/{id}", name="allMatchsbytournois")
     */
    public function allMatchsbytournois(MatchsRepository $matchsRepository,TournoisRepository  $tournoisRepository,$id): Response
    {
       $t= $tournoisRepository->find($id);
        $matchs=$matchsRepository->findBy(array("tournoi"=>$t));


        $jsonData=array();
        $prd=array();
        $i=0;
        foreach ($matchs as $t){

            $prd = array(
                'id' => $t->getId(),
                'tournoi_id' => $t->getTournoi()->getId(),
                'equipe1_id' => $t->getEquipe1()->getId(),
                'equipe2_id' => $t->getEquipe2()->getId(),
                'date' => $t->getDateMatch(),
                'score_a' => $t->getScoreA(),
                'score_b' => $t->getScoreB(),
                'equipe1_nom' => $t->getEquipe1()->getNom(),
                'equipe2_nom' => $t->getEquipe2()->getNom(),
                'ref_match' => $t->getRefMatch(),


            );
            $jsonData[$i++] = $prd;
        }


        return new JsonResponse($jsonData);
    }



    /**
     * @Route("/mobile/allMatchs", name="allMatchs")
     */
    public function allMatchs(MatchsRepository $matchsRepository): Response
    {

        $matchs=$matchsRepository->findAll();


        $jsonData=array();
        $prd=array();
        $i=0;
        foreach ($matchs as $t){

            $prd = array(
                'id' => $t->getId(),
                'tournoi_id' => $t->getTournoi()->getId(),
                'equipe1_id' => $t->getEquipe1()->getId(),
                'equipe2_id' => $t->getEquipe2()->getId(),
                'date' => $t->getDateMatch(),
                'score_a' => $t->getScoreA(),
                'score_b' => $t->getScoreB(),
                'equipe1_nom' => $t->getEquipe1()->getNom(),
                'equipe2_nom' => $t->getEquipe2()->getNom(),
                'ref_match' => $t->getRefMatch(),


            );
            $jsonData[$i++] = $prd;
        }


        return new JsonResponse($jsonData);
    }



}