<?php

namespace App\Controller;

use App\Entity\Recherche;
use App\Form\ContactType;
use Symfony\Component\Mime\Email;
use App\Repository\ChatRepository;
use App\Repository\RechercheRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PlotterController extends AbstractController
{
     /**
     * @Route("/confirm/{chat_id}", name="confirmplot")
     */
    public function confirmplot(
        int $chat_id,
        EntityManagerInterface $em,
        ChatRepository $chatRepository,
        Request $request,
        MailerInterface $mailer
    ): Response {
        $chat = $chatRepository->findOneBy(['id' => $chat_id]);
        $recherche = new Recherche();
        $recherche->setDate(new \DateTime());
        $date = $recherche->getDate()->format('Y-m-d H:i:s');
        $recherche->setCoordx(rand(47390000, 47400000));
        $recherche->setCoordy(rand(610000, 620000));
        $recherche->setChat($chat);
        $form = $this->createForm(ContactType::class, $recherche);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($recherche);
            $em->flush();
        }
        
        if ($chat->getEmail()) {
            $plotemail = (new Email())
                ->from($this->getParameter('mailer_from'))
                ->to($chat->getEmail())
                ->subject('Alerte signalement !')
                ->html('<p>Bonjour, votre chat <b>'.$chat->getNom().'</b> vient d etre repéré ! Connectez-vous à Miaou et rendez-vous dans votre dashboard pour découvrir à quel endroit</p>');
            $mailer->send($plotemail);
        }
        
        
        return $this->render('plotter/confirm.html.twig', [
            'chat' => $chat, 
            'date' => $date,
            'recherche' => $recherche,
            'form' => $form->createView(),
        ]);

    }
    
    /**
     * @Route("/displayplot/{id}", name="displayplot")
     */
    public function displayplot(int $id, ChatRepository $chatRepository, RechercheRepository $rechercheRepository): Response
    {
        $plots = $rechercheRepository->findBy(['chat' => $id]);
        $chat = $chatRepository->findOneBy(['id' => $id]);
        $date = new \DateTime();
        $displaydate = $date->format('Y-m-d H:i:s');
        return $this->render('plotter/display.html.twig', [
            'plots' => $plots, 
            'displaydate' => $displaydate,
            'chat' => $chat,
        ]);
    }
}
