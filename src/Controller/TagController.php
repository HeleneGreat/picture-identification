<?php

namespace App\Controller;

use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TagController extends AbstractController
{

    private $tagRepository;

    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    #[Route('/tags/{id}/delete', name: 'tag_delete', requirements: ['id' => '\d+'])]
    public function tagDelete(int $id, EntityManagerInterface $entityManager): Response
    {
        $tag = $this->tagRepository->find($id);
        $pictureId = $tag->getPicture()->getId();
        if (!$tag) {
            throw $this->createNotFoundException('The tag does not exist');
        }
        $entityManager->remove($tag);
        $entityManager->flush();

        return $this->redirectToRoute('picture_details', ['id' => $pictureId]);
    }
}
