<?php

namespace App\Controller;

use App\Repository\CommentsRepository;
use App\Repository\LinksRepository;
use App\Repository\PostsRepository;
use App\Repository\ShowsRepository;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(ShowsRepository $showsRepository, CommentsRepository $commentsRepository, PostsRepository $postsRepository, UsersRepository $usersRepository, LinksRepository $linksRepository)
    {
        $nbrShows = count($showsRepository->findAll());
        $nbrPosts = count($postsRepository->findAll());
        $nbrComments = count($commentsRepository->findAll());
        $nbrUsers = count($usersRepository->findAll());
        $nbrLinks = count($linksRepository->findAll());

        return $this->render('admin/index.html.twig', [
            'nbrUsers' => $nbrUsers,
            'nbrComments' => $nbrComments,
            'nbrPosts' => $nbrPosts,
            'nbrShows' => $nbrShows,
            'nbrLinks' => $nbrLinks,
        ]);
    }
}
