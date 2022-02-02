<?php

namespace App\Controller;

use App\Entity\Commune;
use App\Repository\ChatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController
{
    /**
     * @Route("/search", name="search")
     */
    public function search(Request $request, ChatRepository $chatRepository): Response 
    {
        $form = $this->createFormBuilder()
        ->add('commune', EntityType::class, [
            'class' => Commune::class,
            'choice_label' => 'nom',
        ])
        ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $commune = $form->get('commune')->getData();
            $chats = $chatRepository->findBy(['commune' => $commune->getNom(), 'islost' => true]);
            return $this->render('display/index.html.twig', [
                'commune' => $commune->getNom(),
                'chats' => $chats,
            ]);
        return $this->render('search/index.html.twig', [
                'form' => $form->createView(),  ]);           
        }
        return $this->render('search/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
