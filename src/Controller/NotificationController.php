<?php

namespace App\Controller;

use App\Entity\Matchs;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use DateTimeZone;

/**
 * @Route("espace_societe/")
 */
class NotificationController extends AbstractController
{
    public function afficherToutNotification(Request $request): Response
    {

        $matchs = $this->getDoctrine()->getRepository(Matchs::class)->findAll();

        $recsJsonn = [];
        $i = 0;

        foreach ($matchs as $match) {
            $jour = (int)$match->getDateMatch()->format("d");
            $moi = (int)$match->getDateMatch()->format("m");
            $annee = (int)$match->getDateMatch()->format("y");
            $heure = $match->getDateMatch()->format("h:i:s");
            $minute = (int)$match->getDateMatch()->format("i");
            $date_now = date("Y-m-d h:i:s");
            $variable = new DateTime($date_now);
            $jourauj = (int)$variable->format("d");
            $moiauj = (int)$variable->format("m");
            $anneeauj = (int)$variable->format("y");
            $heureauj = $variable->format("h:i:s");
            $minuteauj = (int)$variable->format("i");
            //$to_compare = "2018-06-01 12:48:09";
            $variable1 = new DateTime($heureauj);
            $variable2 = new DateTime($heure);
            $difference = (int) date_diff($variable1, $variable2)->format("%h");
           
            if ($annee == $anneeauj) {
                if ($moi == $moiauj) {
                    if ($jourauj = $jour) {
                        if ($difference >= 2) {
                           $recsJsonn[$i] = $match;
                        }
                    }
                }
            }
            $i++;
        }

        asort($matchs);



        return $this->render('notification/afficher_tout_notification.html.twig', [
            'matchs' => $recsJsonn
        ]);
    }
}
