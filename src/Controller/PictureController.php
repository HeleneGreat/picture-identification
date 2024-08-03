<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Form\PictureType;
use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PictureController extends AbstractController
{

    private $pictureRepository;

    public function __construct(PictureRepository $pictureRepository)
    {
        $this->pictureRepository = $pictureRepository;
    }

    #[Route('/pictures', name: 'picture_list')]
    public function pictureListPage(): Response
    {
        $pictures = $this->pictureRepository->findAll();
        return $this->render('picture/picture-list.html.twig', [
            'pictures' => $pictures,
        ]);
    }

    #[Route('/pictures/{id}', name: 'picture_details', requirements: ['id' => '\d+'])]
    public function pictureDetails(int $id): Response
    {
        $picture = $this->pictureRepository->find($id);
        return $this->render(
            'picture/picture-details.html.twig',
            [
                'picture' => $picture,
            ]
        );
    }

    #[Route('/pictures/new', name: 'picture_new')]
    public function pictureNew(Request $request, EntityManagerInterface $entityManager): Response
    {
        $picture = new Picture();
        $form = $this->createForm(PictureType::class, $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $picture = $form->getData();
            $picture->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($picture);
            $entityManager->flush();

            return $this->redirectToRoute('picture_list');
        }

        return $this->render('picture/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/pictures/{id}/update', name: 'picture_update', requirements: ['id' => '\d+'])]
    public function pictureUpdate(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $picture = $this->pictureRepository->find($id);

        if (!$picture) {
            throw $this->createNotFoundException('The picture does not exist');
        }

        $form = $this->createForm(PictureType::class, $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('picture_details', ['id' => $id]);
        }

        return $this->render('picture/update.html.twig', [
            'form' => $form,
            'picture' => $picture,
        ]);
    }

    #[Route('/pictures/{id}/delete', name: 'picture_delete', requirements: ['id' => '\d+'])]
    public function pictureDelete(int $id, EntityManagerInterface $entityManager): Response
    {
        $picture = $this->pictureRepository->find($id);
        if (!$picture) {
            throw $this->createNotFoundException('The picture does not exist');
        }
        $entityManager->remove($picture);
        $entityManager->flush();

        return $this->redirectToRoute('picture_list');
    }
}
