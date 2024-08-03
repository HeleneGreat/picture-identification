<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PictureController extends AbstractController
{
    #[Route('/pictures', name: 'picture_list')]
    public function pictureListPage(): Response
    {
        return $this->render('picture-list.html.twig', []);
    }
}
