<?php

namespace App\Controller;

use App\Entity\Chat;
use App\Form\Chat1Type;
use App\Repository\ChatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * This class is dedicated to cats CRUD
 * @Route("/chat")
 */
class ChatController extends AbstractController
{
    /**
     * @Route("/", name="chat_index", methods={"GET"})
     */
    public function index(ChatRepository $chatRepository): Response
    {
        return $this->render('chat/index.html.twig', [
            'chats' => $chatRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="chat_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $chat = new Chat();
        $form = $this->createForm(Chat1Type::class, $chat);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($chat);
            $entityManager->flush();

            return $this->redirectToRoute('chat_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('chat/new.html.twig', [
            'chat' => $chat,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="chat_show", methods={"GET"})
     */
    public function show(Chat $chat): Response
    {
        return $this->render('chat/show.html.twig', [
            'chat' => $chat,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="chat_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Chat $chat, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Chat1Type::class, $chat);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('chat_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('chat/edit.html.twig', [
            'chat' => $chat,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="chat_delete", methods={"POST"})
     */
    public function delete(Request $request, Chat $chat, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$chat->getId(), $request->request->get('_token'))) {
            $entityManager->remove($chat);
            $entityManager->flush();
        }

        return $this->redirectToRoute('chat_index', [], Response::HTTP_SEE_OTHER);
    }
}
