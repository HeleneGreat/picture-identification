<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Entity\Tag;
use App\Form\PictureType;
use App\Form\TagType;
use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Vich\UploaderBundle\Handler\DownloadHandler;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class PictureController extends AbstractController
{

    private $pictureRepository;

    public function __construct(PictureRepository $pictureRepository)
    {
        $this->pictureRepository = $pictureRepository;
    }

    #[Route('/pictures', name: 'picture_list')]
    public function pictureListPage(UploaderHelper $uploaderHelper): Response
    {
        $pictures = $this->pictureRepository->findAll();
        // $imagePath = $uploaderHelper->asset($picture, 'imageFile');
        return $this->render('picture/picture-list.html.twig', [
            'pictures' => $pictures,
        ]);
    }

    #[Route('/pictures/{id}', name: 'picture_details', requirements: ['id' => '\d+'])]
    public function pictureDetails(int $id, Request $request, EntityManagerInterface $entityManager, UploaderHelper $uploaderHelper): Response
    {
        $picture = $this->pictureRepository->find($id);
        $imagePath = $uploaderHelper->asset($picture, 'imageFile');

        $tag = new Tag();
        $tag->setPicture($picture);
        $tagForm = $this->createForm(TagType::class, $tag);
        $tagForm->handleRequest($request);

        if ($tagForm->isSubmitted() && $tagForm->isValid()) {
            $tag = $tagForm->getData();
            $tag->setCreatedAt(new \DateTimeImmutable());
            $tag->setPicture($picture);

            $entityManager->persist($tag);
            $entityManager->flush();

            return $this->redirectToRoute('picture_details', ['id' => $picture->getId()]);
        }


        return $this->render(
            'picture/picture-details.html.twig',
            [
                'picture' => $picture,
                'imagePath' => $imagePath,
                'tagForm' => $tagForm,
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

    #[Route('/uploads/pictures/{id}/view', name: 'picture_view')]
    public function viewPicture(Picture $picture, DownloadHandler $downloadHandler): Response
    {
        // Affiche le fichier inline dans le navigateur
        return $downloadHandler->downloadObject($picture, 'imageFile', null, null, false);
    }
}
