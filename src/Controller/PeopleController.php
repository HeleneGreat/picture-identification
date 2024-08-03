<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PeopleController extends AbstractController
{
    #[Route('/people', name: 'people_list')]
    public function peopleListPage(): Response
    {

        return $this->render('people-list.html.twig', []);
    }
}
