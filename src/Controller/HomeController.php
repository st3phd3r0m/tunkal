<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Posts;
use App\Entity\Shows;
use App\Form\CommentsType;
use App\Repository\CommentsRepository;
use App\Repository\LinksRepository;
use App\Repository\PostsRepository;
use App\Repository\ShowsRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class HomeController extends AbstractController
{
    private PostsRepository $postsRepository;
    private ShowsRepository $showsRepository;
    private CommentsRepository $commentsRepository;

    public function __construct(PostsRepository $postsRepository, ShowsRepository $showsRepository, CommentsRepository $commentsRepository)
    {
        $this->postsRepository = $postsRepository;
        $this->showsRepository = $showsRepository;
        $this->commentsRepository = $commentsRepository;
    }

    /**
     * @Route("/", name="home")
     */
    public function index(LinksRepository $linksRepository): Response
    {
        $post = $this->newFirmPage('accueil');

        $copyrightLink = $linksRepository->findBy(['position' => 'copyright'])[0];
        $mediaLinks = $linksRepository->findBy(['position' => 'media']);

        return $this->render('home/index.html.twig', [
            'post' => $post,
            'mediaLinks' => $mediaLinks,
            'copyrightLink' => $copyrightLink,
        ]);
    }

    /**
     * @Route("/news/", name="news", methods={"GET"})
     */
    public function indexPosts(PaginatorInterface $paginator, Request $request): Response
    {
        $post = $this->newFirmPage('news');
        $posts = $paginator->paginate(
            // $postsRepository->findBy(['is_past_concert'=>0], ['created_at'=>'DESC']),
            $this->postsRepository->findPosts(),
            //Le numero de la page, si aucun numero, on force la page 1
            $request->query->getInt('page', 1),
            //Nombre d'??l??ment par page
            10
        );

        return $this->render('home/news.html.twig', [
            'posts' => $posts,
            'post' => $post,
        ]);
    }

    /**
     * @Route("/previous/concerts/", name="pastConcerts", methods={"GET"})
     */
    public function indexPastConcerts(PaginatorInterface $paginator, Request $request): Response
    {
        $post = $this->newFirmPage('previous-concerts');
        $posts = $paginator->paginate(
            $this->postsRepository->findBy(['is_past_concert' => 1], ['created_at' => 'DESC']),
            //Le numero de la page, si aucun numero, on force la page 1
            $request->query->getInt('page', 1),
            //Nombre d'??l??ment par page
            10
        );

        return $this->render('home/news.html.twig', [
            'posts' => $posts,
            'post' => $post,
        ]);
    }

    /**
     * @Route("/upcoming/concerts/", name="upcomingShows", methods={"GET"})
     */
    public function indexUpcomingShows(PaginatorInterface $paginator, Request $request): Response
    {
        $post = $this->newFirmPage('upcoming-concerts');
        $shows = $paginator->paginate(
            $this->showsRepository->findExpectedConcerts(),
            //Le numero de la page, si aucun numero, on force la page 1
            $request->query->getInt('page', 1),
            //Nombre d'??l??ment par page
            10
        );

        return $this->render('home/concerts.html.twig', [
            'shows' => $shows,
            'post' => $post,
        ]);
    }

    /**
     * @Route("/news/{slug}", name="post", methods={"GET", "POST"})
     * @Route("/previous/concerts/{slug}", name="pastConcert", methods={"GET", "POST"})
     */
    public function showPost(Posts $post, Request $request): Response
    {
        //Instanciation de Comments, cr??ation formulaire commentaire
        $comment = new Comments();
        $formComment = $this->createForm(CommentsType::class, $comment);
        $formComment->handleRequest($request);

        //Soumission formulaire commentaire
        if ($formComment->isSubmitted() && $formComment->isValid()) {
            $comment->setPost($post);
            $comment->setSentAt(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
            $comment->setIsModerated(false);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            //Envoi d'un message utilisateur
            $this->addFlash('success', 'Commentaire enregistr??');

            $_route = $request->get('_route');

            return $this->redirectToRoute($_route, [
                'slug' => $post->getSlug(),
            ]);
        }

        return $this->render('home/post.html.twig', [
            'post' => $post,
            'numbOfCommentsPages' => $this->commentsRepository->getNumberOfPages($post->getSlug()),
            'form' => $formComment->createView(),
        ]);
    }

    // /**
    //  * @Route("/upcoming/concerts/{slug}", name="upcomingShow", methods={"GET"})
    //  */
    // public function showOneShow(Shows $show): JsonResponse
    // {
    //     return new JsonResponse((array) $show);
    // }

    /**
     * Fonction qui initialise les metadonn??es et le contenu de publications d' "usine".
     *
     * @param string $slug
     *
     * @return Posts $post
     */
    public function newFirmPage(string $slug = null)
    {
        $post = $this->postsRepository->findOneBy(['slug' => $slug]);
        $slug = ucfirst(str_replace('-', ' ', $slug));

        if (!$post) {
            //Instanciation entit?? Posts
            $post = new Posts();
            $post->setTitle($slug);
            $post->setMetaTitle($slug);
            $post->setContent($slug);
            $post->setMetaDescription($slug);
            $keywords = [$slug];
            $post->setKeyWords($keywords);
            $post->setMetaKeywords($keywords);
            $post->setCreatedAt(new \DateTime('now'));
            $post->setIsPastConcert(false);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();
        }

        return $post;
    }
}
