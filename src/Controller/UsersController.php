<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\UsersType;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/admin/users")
 */
class UsersController extends AbstractController
{
    /**
     * @Route("/", name="users_index", methods={"GET"})
     */
    public function index(UsersRepository $usersRepository): Response
    {
        return $this->render('users/index.html.twig', [
            'users' => $usersRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="users_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new Users();
        $form = $this->createForm(UsersType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            //Envoi d'un message utilisateur
            $this->addFlash('success', 'Nouvel utilisateur créé');

            return $this->redirectToRoute('users_index');
        }

        return $this->render('users/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="users_show", methods={"GET"})
     */
    public function show(Users $user): Response
    {
        return $this->render('users/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="users_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Users $user): Response
    {
        $form = $this->createForm(UsersType::class, $user);
        $form->remove('plainPassword');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            //Envoi d'un message utilisateur
            $this->addFlash('success', 'Utilisateur modifié.');

            return $this->redirectToRoute('users_index');
        }

        return $this->render('users/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/change/password", name="users_change_password", methods={"GET","POST"})
     */
    public function changePassword(Request $request, Users $user, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        if ($this->getUser()->getUserName() === $user->getUsername()) {
            $form = $this->createForm(UsersType::class, $user);
            $form->remove('email');
            $form->remove('roles');
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // encode the plain password
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );

                $this->getDoctrine()->getManager()->flush();

                //Envoi d'un message utilisateur
                $this->addFlash('success', 'Votre mot de passe a bien été modifié');

                return $this->redirectToRoute('users_index');
            } elseif ($form->isSubmitted() && !$form->isValid()) {
                //Envoi d'un message utilisateur
                $this->addFlash('fail', 'Formulaire incomplet ou invalide.');
            }

            return $this->render('users/edit.html.twig', [
                'changepassword' => true,
                'user' => $user,
                'form' => $form->createView(),
            ]);
        }

        //Envoi d'un message utilisateur
        $this->addFlash('fail', 'Vous devez être connecté en tant que '.$user->getEmail().' pour modifier le mot de passe associé à ce compte.');

        return $this->redirectToRoute('users_index');
    }

    /**
     * @Route("/{id}", name="users_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Users $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
            //Envoi d'un message utilisateur
            $this->addFlash('success', 'Utilisateur supprimé.');
        }

        return $this->redirectToRoute('users_index');
    }
}
