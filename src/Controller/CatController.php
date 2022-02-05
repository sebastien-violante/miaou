<?php

namespace App\Controller;

use App\Entity\Chat;
use App\Entity\Race;
use App\Form\ChatType;
use App\Entity\Commune;
use App\Repository\ChatRepository;
use App\Repository\MessageRepository;
use App\Repository\RechercheRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class CatController extends AbstractController
{
    /**
     * This controller displays all the cats selected by user's email
     * @Route("/cat", name="cat")
     */
    public function index(ChatRepository $chatRepository): Response
    {
        $mail = $this->getUser()->getEmail();
        $chats = $chatRepository->findBy(['email' => $mail]);
        return $this->render('cat/index.html.twig', [
            'chats' => $chats,
        ]);
    }

    /**
     * This controller allows to register a new cat
     * @Route("/catregistration", name="catregistration")
     */
    public function catregistration(
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $chat = new Chat();
        $form = $this->createForm(ChatType::class, $chat);
        $form->handleRequest($request);
        $mail = $this->getUser()->getEmail();
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $chat->setEmail($mail);
            $photo = $form->get('photo')->getData();
            if ($photo instanceof UploadedFile && $chat instanceof Chat) {
                $newFilename = 'avatar' . '-' . $chat->getNom() . '.' . $photo->guessExtension();
                if (is_string($this->getParameter('pictures_directory'))) {
                    try {
                        $photo->move(
                            $this->getParameter('pictures_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        return $this->render('errors/error500.html.twig');
                    }
                }
                $chat->setPhoto($newFilename);
            }
            $em->persist($chat);
            $em->flush();
            return $this->redirectToRoute('home');
        }
        return $this->render('cat/registration.html.twig', [
            'form' => $form->createView(),
        ]);
    } 
    
    /**
     * THis controller is used to declare a cat as lost usign its id
     * @Route("/lost/{id}", name="lostbyid")
     */
    public function islost(ChatRepository $chatRepository, EntityManagerInterface $em, int $id): Response
    {
        $chat = $chatRepository->findOneById($id);
        $chat->setIsLost(true);
        $chat->setDatelost(new \DateTime());
        $em->persist($chat);
        $em->flush();
        return $this->render('cat/lost.html.twig', [
            'chat' => $chat,
        ]);
    }

    /**
     * This controller is used to declare a cat as recovered with a research by cat id
     * @Route("/found/{id}", name="foundbyid")
     */
    public function isfound(
        ChatRepository $chatRepository,
        RechercheRepository $rechercheRepository,
        EntityManagerInterface $em,
        int $id
    ): Response {
        $chat = $chatRepository->findOneById($id);
        $chat->setIsLost(false);
        $messages = $rechercheRepository->findby(['chat' => $id]);
        foreach($messages as $message){
            $message->setChat(null);
        }
        $em->persist($chat);
        $em->flush();
        return $this->render('cat/found.html.twig', [
            'chat' => $chat,
        ]);
    }
}