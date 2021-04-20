<?php

namespace App\Controller;

use App\Entity\Shows;
use App\Form\ShowsType;
use App\Repository\ShowsRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShowsController extends AbstractController
{
    /**
     * @Route("/admin/shows/", name="shows_index", methods={"GET"})
     */
    public function index(ShowsRepository $showsRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $shows = $paginator->paginate(
            $showsRepository->findAll(),
            //Le numero de la page, si aucun numero, on force la page 1
            $request->query->getInt('page', 1),
            //Nombre d'élément par page
            10
        );

        return $this->render('shows/index.html.twig', [
            'shows' => $shows,
        ]);
    }

    /**
     * @Route("/admin/shows/new", name="shows_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $show = new Shows();
        $form = $this->createForm(ShowsType::class, $show, [
            'validation_groups' => ['new'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Récupération des mots-clés en tant que chaine de caractères et séparation en array avec un délimiteur ";"
            $keywords = $form->get('keywords')->getData();
            $keywords = explode('#', $keywords);
            $keywords = array_filter($keywords);
            $show->setKeywords($keywords);

            $keywords = $form->get('metaKeywords')->getData();
            $keywords = explode('#', $keywords);
            $keywords = array_filter($keywords);
            $show->setMetaKeywords($keywords);

            $show->setUpdatedAt(new \DateTime('now', new \DateTimeZone('Europe/Paris')));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($show);
            $entityManager->flush();

            //Envoi d'un message utilisateur
            $this->addFlash('success', 'Nouveau concert créé.');

            return $this->redirectToRoute('shows_index');
        }

        return $this->render('shows/new.html.twig', [
            'show' => $show,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/shows/{id}", name="shows_show", methods={"GET"})
     */
    public function show(Shows $show): Response
    {
        return $this->render('shows/show.html.twig', [
            'show' => $show,
        ]);
    }

    /**
     * @Route("/admin/shows/{id}/edit", name="shows_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Shows $show, Filesystem $filesystem): Response
    {
        $form = $this->createForm(ShowsType::class, $show, [
            'validation_groups' => ['update'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $show->setUpdatedAt(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
            //Stockage de l'ancien nom de fichier image
            $oldImage = $show->getImage();

            $this->getDoctrine()->getManager()->flush();

            //Stockage du nouveau nom de fichier après persistement en bdd
            $newImage = $show->getImage();
            //Si le nom de fichier a changé
            if ($oldImage != $newImage) {
                $oldImage = '../public/media/cache/miniatures/images/'.$oldImage;
                if ($filesystem->exists($oldImage)) {
                    //Alors on supprime la miniature correspondante
                    $filesystem->remove($oldImage);
                }
            }
            //Envoi d'un message utilisateur
            $this->addFlash('success', 'Le concert a bien été modifié.');

            return $this->redirectToRoute('shows_index');
        }

        return $this->render('shows/edit.html.twig', [
            'show' => $show,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/shows/{id}", name="shows_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Shows $show, Filesystem $filesystem): Response
    {
        if ($this->isCsrfTokenValid('delete'.$show->getId(), $request->request->get('_token'))) {
            //Stockage de l'ancien nom de fichier image
            $oldImage = $show->getImage();

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->remove($show);
            $entityManager->flush();

            //Suppression de la miniature associée à la langue de publication
            $oldImage = '../public/media/cache/miniatures/images/'.$oldImage;
            //Si le fichier existe
            if ($filesystem->exists($oldImage)) {
                //Alors on le supprime
                $filesystem->remove($oldImage);
            }
            //Envoi d'un message utilisateur
            $this->addFlash('success', 'Le concert a bien été supprimé.');
        }

        return $this->redirectToRoute('shows_index');
    }

    /**
     * @Route("/admin/api/shows", name="give_shows", methods={"GET"})
     */
    public function giveShows(Request $request, ShowsRepository $showsRepository): JsonResponse
    {
        if ($request->isXmlHttpRequest()) {
            $token = $request->headers->get('authorization');
            if (!$this->isCsrfTokenValid('links', $token)) {
                return new JsonResponse('Unauthorized', 401);
            }
            $shows = $showsRepository->getShows();
            return new JsonResponse($shows, 200);
        }

        return new JsonResponse('Méthode non-autorisée', 405);
    }
}
