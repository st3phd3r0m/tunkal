<?php

namespace App\Controller;

use App\Repository\CommentsRepository;
use App\Repository\LinksRepository;
use App\Repository\PostsRepository;
use App\Repository\ShowsRepository;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    private ShowsRepository $showsRepository;
    private CommentsRepository $commentsRepository;
    private PostsRepository $postsRepository;
    private UsersRepository $usersRepository;
    private LinksRepository $linksRepository;

    public function __construct(ShowsRepository $showsRepository, CommentsRepository $commentsRepository, PostsRepository $postsRepository, UsersRepository $usersRepository, LinksRepository $linksRepository)
    {
        $this->commentsRepository = $commentsRepository;
        $this->showsRepository = $showsRepository;
        $this->postsRepository = $postsRepository;
        $this->usersRepository = $usersRepository;
        $this->linksRepository = $linksRepository;
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        $nbrShows = count($this->showsRepository->findAll());
        $nbrPosts = count($this->postsRepository->findAll());
        $nbrComments = count($this->commentsRepository->findAll());
        $nbrUsers = count($this->usersRepository->findAll());
        $nbrLinks = count($this->linksRepository->findAll());

        return $this->render('admin/index.html.twig', [
            'nbrUsers' => $nbrUsers,
            'nbrComments' => $nbrComments,
            'nbrPosts' => $nbrPosts,
            'nbrShows' => $nbrShows,
            'nbrLinks' => $nbrLinks,
        ]);
    }
}
