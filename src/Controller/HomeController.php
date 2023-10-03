<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class for homepage
 * 
 * @return Reponse
 */
class HomeController extends AbstractController
{
    #[Route('/', name : 'app.home', methods: ['GET'])]
    public function homepage(): Response 
    {
        $data = ['Pierre','Paul','Jacques'];
        return $this->render('index.html.twig', [
            'donnees' => $data,
        ]);
    }
}
