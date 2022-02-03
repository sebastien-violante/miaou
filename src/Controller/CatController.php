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
     * Enregistrement d'un nouveau chat
     * @Route("/catregistration", name="catregistration")
     */
    public function catregistration(
        Request $request,
        EntityManagerInterface $em
    ): Response
    {
        $chat = new Chat();
        $form = $this->createForm(ChatType::class, $chat);
        $form->handleRequest($request);
        $mail = $this->getUser()->getEmail();
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $chat->setEmail($mail);
            // Persist Category Object
            $photo = $form->get('photo')->getData();

            // this condition is needed because the 'picuture' field is not required
            if ($photo instanceof UploadedFile && $chat instanceof Chat) {
                $newFilename = 'avatar' . '-' . $chat->getNom() . '.' . $photo->guessExtension();
                // Move the file to the directory where brochures are stored
                if (is_string($this->getParameter('pictures_directory'))) {
                    try {
                        $photo->move(
                            $this->getParameter('pictures_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                        return $this->render('errors/error500.html.twig');
                    }
                }
                // instead of its contents
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