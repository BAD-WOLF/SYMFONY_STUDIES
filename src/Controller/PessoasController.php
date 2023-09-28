<?php

namespace App\Controller;

use App\Entity\Pessoas;
use App\Form\PessoasType;
use App\Repository\PessoasRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PessoasController extends AbstractController
{
    #[Route(path: ['/', '/home'], name: 'app_pessoas_index', methods: ['GET'])]
    public function index(PessoasRepository $pessoasRepository): Response
    {
        return $this->render('pessoas/index.html.twig', [
            'pessoas' => $pessoasRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_pessoas_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $pessoa = new Pessoas();
        $form = $this->createForm(PessoasType::class, $pessoa);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($pessoa);
            $entityManager->flush();

            return $this->redirectToRoute('app_pessoas_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pessoas/new.html.twig', [
            'pessoa' => $pessoa,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_pessoas_show', methods: ['GET'])]
    public function show(Pessoas $pessoa): Response
    {
        return $this->render('pessoas/show.html.twig', [
            'pessoa' => $pessoa,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_pessoas_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Pessoas $pessoa, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PessoasType::class, $pessoa);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_pessoas_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pessoas/edit.html.twig', [
            'pessoa' => $pessoa,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_pessoas_delete', methods: ['POST'])]
    public function delete(Request $request, Pessoas $pessoa, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$pessoa->getId(), $request->request->get('_token'))) {
            $entityManager->remove($pessoa);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_pessoas_index', [], Response::HTTP_SEE_OTHER);
    }
}
