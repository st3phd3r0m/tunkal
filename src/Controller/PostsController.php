<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Form\PostsType;
use App\Repository\PostsRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostsController extends AbstractController
{
    /**
     * @Route("/admin/posts/", name="posts_index", methods={"GET"})
     */
    public function index(PostsRepository $postsRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $posts = $paginator->paginate(
            $postsRepository->findAll(),
            //Le numero de la page, si aucun numero, on force la page 1
            $request->query->getInt('page', 1),
            //Nombre d'élément par page
            10
        );

        return $this->render('posts/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    /**
     * @Route("/admin/posts/new", name="posts_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $post = new Posts();
        $form = $this->createForm(PostsType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //On récupère les instances de l'entité Images, instanciées lors de la collection dans le formulaire d'ajout d'images
            $images = $post->getImages();
            //Et pour chacune de ces instances,
            foreach ($images as $key => $image) {
                //on fait le lien avec l'objet post
                $image->setPosts($post);
                $images->set($key, $image);
            }

            $keywords = $form->get('keywords')->getData();
            $keywords = explode('#', $keywords);
            $keywords = array_filter($keywords);
            $post->setKeywords($keywords);

            $keywords = $form->get('metaKeywords')->getData();
            $keywords = explode('#', $keywords);
            $keywords = array_filter($keywords);
            $post->setMetaKeywords($keywords);

            $post->setCreatedAt(new \DateTime('now', new \DateTimeZone('Europe/Paris')));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            //Envoi d'un message utilisateur
            $this->addFlash('success', 'Nouvel article créé.');

            return $this->redirectToRoute('posts_index');
        }

        return $this->render('posts/new.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/posts/{id}", name="posts_show", methods={"GET"})
     */
    public function show(Posts $post): Response
    {
        return $this->render('posts/show.html.twig', [
            'post' => $post,
        ]);
    }

    /**
     * @Route("/admin/posts/{id}/edit", name="posts_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Posts $post, Filesystem $filesystem): Response
    {
        //Récupération des noms de fichiers images pour suppression ultérieure des miniatures
        $images = $post->getImages();
        $oldImages = [];
        foreach ($images as $key => $image) {
            $oldImages[] = $image->getName();
        }

        $form = $this->createForm(PostsType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //on appelle le manager d'entité
            $entityManager = $this->getDoctrine()->getManager();

            //On récupère les instances de l'entité Images, instanciées lors de la collection dans le formulaire d'ajout d'images
            $images = $post->getImages();

            $images_names = [];
            //Et pour chacune de ces instances,
            foreach ($images as $key => $image) {
                $images_names[] = $image->getName();
                if (null === $image->getName() && null !== $image->getImageFile()) {
                    //Si l'utilisateur ajoute une image
                    //on ajoute l'objet post comme attribut de l'objet image
                    $image->setPosts($post);
                    $images->set($key, $image);
                } elseif (null === $image->getName() && null === $image->getImageFile()) {
                    //Si l'utilisateur veut la suppression d'une des images dans la collection
                    //Alors on enlève l'objet image correspondant de l'objet product
                    $post->removeImage($image);
                    //Et on l'enlève de la bdd avec le manager d'entité
                    $entityManager->remove($image);
                } elseif (null !== $image->getName() && null === $image->getImageFile()) {
                    //Si l'utilisateur veut garder l'image dans la collection
                    //on ajoute l'objet product comme attribut de l'objet image
                    $image->setPosts($post);
                    $images->set($key, $image);
                }
            }

            //Récupération des mots-clés en tant que chaine de caractères et séparation en array avec un délimiteur ";"
            $keywords = $form->get('keywords')->getData();
            $keywords = explode('#', $keywords);
            $keywords = array_filter($keywords);
            $post->setKeywords($keywords);

            $keywords = $form->get('metaKeywords')->getData();
            $keywords = explode('#', $keywords);
            $keywords = array_filter($keywords);
            $post->setMetaKeywords($keywords);

            $post->setCreatedAt(new \DateTime('now', new \DateTimeZone('Europe/Paris')));

            $entityManager->flush();

            //Suppression des fichiers miniatures
            $media_cache = '../public/media/cache/';
            if ($filesystem->exists($media_cache)) {
                $filesystem->remove($media_cache);
            }

            $files_to_delete = array_diff($oldImages, $images_names);
            foreach ($files_to_delete as $key => $image) {
                $file_to_delete = '../public/images/'.$image;
                if ($filesystem->exists($file_to_delete)) {
                    $filesystem->remove($file_to_delete);
                }
            }

            //Envoi d'un message utilisateur
            $this->addFlash('success', 'L\'article a bien été modifié.');

            return $this->redirectToRoute('posts_index');
        }

        return $this->render('posts/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/posts/{id}", name="posts_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Posts $post, Filesystem $filesystem): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
            //Appel du manager de Doctrine
            $entityManager = $this->getDoctrine()->getManager();

            //Suppression des images et des miniatures associées au produit
            $images = $post->getImages();
            foreach ($images as $image) {
                $miniature = '../public/media/cache/min300/images/'.$image->getName();
                //Si le fichier existe
                if ($filesystem->exists($miniature)) {
                    //Alors on supprime la miniature correspondante
                    $filesystem->remove($miniature);
                }
                $post->removeImage($image);
                $entityManager->remove($image);
            }

            //Commentaires associés au produit
            $comments = $post->getComments();
            foreach ($comments as $comment) {
                //Rupture entre les commentaires et le produit
                $post->removeComment($comment);
                //Suppression des commentaires si l'utilisateur le décide
                if ('true' === $request->request->get('delete_related')) {
                    $entityManager->remove($comment);
                }
            }

            //On supprime le produit
            $entityManager->remove($post);
            //La ligne ci-dessous rend la suppression en BDD effective
            $entityManager->flush();

            //Envoi d'un message utilisateur
            $this->addFlash('success', 'L\'article a bien été supprimé.');
        }

        return $this->redirectToRoute('posts_index');
    }

    /**
     * @Route("/admin/api/posts", name="give_posts", methods={"GET"})
     */
    public function givePosts(Request $request, PostsRepository $postsRepository): JsonResponse
    {
        if ($request->isXmlHttpRequest()) {
            $token = $request->headers->get('authorization');
            if (!$this->isCsrfTokenValid('links', $token)) {
                return new JsonResponse('Unauthorized', 401);
            }
            $isPastConcert= (bool) $request->query->get('isPastConcert');
            $posts = $postsRepository->getPosts($isPastConcert);
            return new JsonResponse($posts, 200);
        }

        return new JsonResponse('Méthode non-autorisée', 405);
    }
}
