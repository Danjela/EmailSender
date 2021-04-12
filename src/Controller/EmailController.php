<?php

namespace App\Controller;

use App\Entity\Email;
use App\Form\EmailType;
use App\Repository\EmailRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/email')]
class EmailController extends AbstractController
{
    #[Route('/', name: 'email_index', methods: ['GET'])]
    public function index(EmailRepository $emailRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'User tried to access a page without having ROLE_USER');
        return $this->render('email/index.html.twig', [
            'emails' => $emailRepository->findBy(array('user' => $this->getUser()),$orderBy = null, $limit = null, $offset = null),
        ]);
    }

    #[Route('/new', name: 'email_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'User tried to access a page without having ROLE_USER');
        $email = new Email();
        $form = $this->createForm(EmailType::class, $email);
        $form->handleRequest($request);
        $email->setUser($this->getUser());

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($email);
            $entityManager->flush();

            return $this->redirectToRoute('email_index');
        }

        return $this->render('email/new.html.twig', [
            'email' => $email,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'email_show', methods: ['GET'])]
    public function show(Email $email): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'User tried to access a page without having ROLE_USER');
        return $this->render('email/show.html.twig', [
            'email' => $email,
        ]);
    }

    #[Route('/{id}/edit', name: 'email_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Email $email): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'User tried to access a page without having ROLE_USER');
        $form = $this->createForm(EmailType::class, $email);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('email_index');
        }

        return $this->render('email/edit.html.twig', [
            'email' => $email,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'email_delete', methods: ['POST'])]
    public function delete(Request $request, Email $email): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'User tried to access a page without having ROLE_USER');
        if ($this->isCsrfTokenValid('delete'.$email->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($email);
            $entityManager->flush();
        }

        return $this->redirectToRoute('email_index');
    }
}
