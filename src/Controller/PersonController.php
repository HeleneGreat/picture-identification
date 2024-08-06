<?php

namespace App\Controller;

use App\Entity\Person;
use App\Form\PersonType;
use App\Repository\PersonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PersonController extends AbstractController
{

    private $personRepository;

    public function __construct(PersonRepository $personRepository)
    {
        $this->personRepository = $personRepository;
    }

    #[Route('/persons', name: 'person_list')]
    public function personListPage(): Response
    {
        $persons = $this->personRepository->findAll();
        return $this->render('person/person-list.html.twig', [
            'persons' => $persons,
        ]);
    }

    #[Route('/persons/{id}', name: 'person_details', requirements: ['id' => '\d+'])]
    public function personDetails(int $id): Response
    {
        $person = $this->personRepository->find($id);
        return $this->render(
            'person/person-details.html.twig',
            [
                'person' => $person,
            ]
        );
    }

    #[Route('/persons/new', name: 'person_new')]
    public function personNew(Request $request, EntityManagerInterface $entityManager): Response
    {
        $person = new Person();
        $form = $this->createForm(PersonType::class, $person);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $person = $form->getData();
            $person->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($person);
            $entityManager->flush();

            return $this->redirectToRoute('person_list');
        }

        return $this->render('person/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/persons/{id}/update', name: 'person_update', requirements: ['id' => '\d+'])]
    public function personUpdate(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $person = $this->personRepository->find($id);

        if (!$person) {
            throw $this->createNotFoundException('The person does not exist');
        }

        $form = $this->createForm(PersonType::class, $person);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('person_details', ['id' => $id]);
        }

        return $this->render('person/update.html.twig', [
            'form' => $form,
            'person' => $person,
        ]);
    }

    #[Route('/persons/{id}/delete', name: 'person_delete', requirements: ['id' => '\d+'])]
    public function personDelete(int $id, EntityManagerInterface $entityManager): Response
    {
        $person = $this->personRepository->find($id);
        if (!$person) {
            throw $this->createNotFoundException('The person does not exist');
        }
        $entityManager->remove($person);
        $entityManager->flush();

        return $this->redirectToRoute('person_list');
    }
}
