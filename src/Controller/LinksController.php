<?php

namespace App\Controller;

use App\Entity\Links;
use App\Form\LinksType;
use App\Repository\LinksRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/links")
 */
class LinksController extends AbstractController
{
    /**
     * @Route("/", name="links_index", methods={"GET"})
     */
    public function index(LinksRepository $linksRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $position = $request->get('positionFilter');
        $isActive = $request->get('isActiveFilter');

        if ($position && null != $isActive) {
            $LinksBuffer = $linksRepository->findBy(['position' => $position, 'is_active' => $isActive]);
        } elseif ($position) {
            $LinksBuffer = $linksRepository->findBy(['position' => $position]);
        } elseif (null != $isActive) {
            $LinksBuffer = $linksRepository->findBy(['is_active' => $isActive]);
        } else {
            $LinksBuffer = $linksRepository->findAll();
        }

        $links = $paginator->paginate(
            $LinksBuffer,
            //Le numero de la page, si aucun numero, on force la page 1
            $request->query->getInt('page', 1),
            //Nombre d'élément par page
            10
        );

        return $this->render('links/index.html.twig', [
            'links' => $links,
        ]);
    }

    /**
     * @Route("/new", name="links_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $link = new Links();
        $form = $this->createForm(LinksType::class, $link);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $link->setUpdatedAt(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($link);
            $entityManager->flush();
            //Envoi d'un message utilisateur
            $this->addFlash('success', 'Le lien a bien été ajouté.');

            return $this->redirectToRoute('links_index');
        }

        return $this->render('links/new.html.twig', [
            'link' => $link,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="links_show", methods={"GET"})
     */
    public function show(Links $link): Response
    {
        return $this->render('links/show.html.twig', [
            'link' => $link,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="links_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Links $link): Response
    {
        $form = $this->createForm(LinksType::class, $link);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $link->setUpdatedAt(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
            $this->getDoctrine()->getManager()->flush();

            //Envoi d'un message utilisateur
            $this->addFlash('success', 'Le lien a bien été modifié.');

            return $this->redirectToRoute('links_index');
        }

        return $this->render('links/edit.html.twig', [
            'link' => $link,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="links_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Links $link): Response
    {
        if ($this->isCsrfTokenValid('delete'.$link->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($link);
            $entityManager->flush();

            //Envoi d'un message utilisateur
            $this->addFlash('success', 'Le lien a bien été supprimé.');
        }

        return $this->redirectToRoute('links_index');
    }
}
