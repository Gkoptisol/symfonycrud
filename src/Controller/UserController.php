<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserController extends AbstractController
{

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * Contact list
     */
    #[Route('/', name: 'app_user')]
    public function index(): Response
    {
        $user = $this->entityManager->getRepository(User::class)->findAll();

        return $this->render('user/index.html.twig', [
            'users' => $user
        ]);
    }

    /**
     * Contact new
     */
    #[Route('/new', name: 'new_user')]
    public function new(Request $request)
    {
        $user = new user();
        $form = $this->createFormBuilder($user)
            ->add('email', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('password', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('save', SubmitType::class, array('label' => 'Create', 'attr' => array('class' => 'btn btn-primary mt-3')))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            return $this->redirectToRoute('app_user');
        }
        return $this->render('user/new.html.twig', array('form' => $form->createView()));
    }

    /**
     * Contact edit
     */
    #[Route('/edit/{id}', name: 'edit_user', methods: ['GET'])]
    public function edit(Request $request, $id)
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        $form = $this->createFormBuilder($user)
            ->add('email', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('password', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('save', SubmitType::class, array('label' => 'Update', 'attr' => array('class' => 'btn btn-primary mt-3')))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            return $this->redirectToRoute('app_user');
        }
        return $this->render('user/edit.html.twig', array('form' => $form->createView()));
    }

    /**
     * Contact view
     */
    #[Route('/{id}', name: 'user_show', methods: ['GET'])]
    public function show($id)
    {
        $user = $this->entityManager->getRepository(user::class)->find($id);
        return $this->render('user/show.html.twig', array('user' => $user));
    }


    /**
     * Contact delete
     */
    #[Route('/delete/{id}', name: 'user_delete')]
    public function delete(Request $request, $id)
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        $this->entityManager->remove($user);
        $this->entityManager->flush();
        $response = new Response();
        $response->send();
        return $this->redirectToRoute('app_user');
    }
}
