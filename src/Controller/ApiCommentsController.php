<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Form\CommentsType;
use App\Repository\CommentsRepository;
use App\Repository\PostsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ApiCommentsController extends AbstractController
{
    private $commentsRepository;

    public function __construct(CommentsRepository $commentsRepository)
    {
        $this->commentsRepository = $commentsRepository;
    }

    /**
     * @Route("/api/comments/post/{slug}", name="comments_list_onpost_api", methods={"GET"} )
     */
    public function listAction(string $slug, Request $request): JsonResponse
    {
        if ($request->isXmlHttpRequest()) {
            $page = $request->query->get("page");
            if (!is_numeric($page)) {
                return new JsonResponse('Bad request : no page number specified', Response::HTTP_BAD_REQUEST);
            }
            if ($page + 1 > $this->commentsRepository->getNumberOfPages($slug)) {
                return new JsonResponse('', Response::HTTP_NO_CONTENT);
            }
            $comments = $this->commentsRepository->getPage($page, $slug);
            return new JsonResponse($comments);
        }
        return new JsonResponse('Method Not Allowed', 405);
    }

    /**
     * @Route("/admin/api/comments/{id}", name="comments_delete_api", methods={"DELETE"})
     */
    public function deleteAction($id, Request $request): JsonResponse
    {
        if ($request->isXmlHttpRequest()) {
            $token = $request->headers->get('authorization');
            $comment = $this->commentsRepository->find($id);
            if (!$this->isCsrfTokenValid('delete' . $id, $token)) {
                return new JsonResponse('Needs authentication', Response::HTTP_UNAUTHORIZED);
            } elseif ($comment == null) {
                return new JsonResponse('Not found', 404);
            } else {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($comment);
                $entityManager->flush();
                return new JsonResponse('OK', 200);
            }
        }
        return new JsonResponse('Method Not Allowed', 405);
    }

    // /**
    //  * @Route("/api/comments/", name="comments_list_api", methods={"GET"})
    //  * @Route("/api/comments/post/{postId}", name="comments_list_onpost_api", methods={"GET"} )
    //  */
    // public function listAction(?int $postId, Request $request): JsonResponse
    // {
    //     if ($request->isXmlHttpRequest()) {
    //         $page = $request->query->get("page");
    //         if (!is_numeric($page)) {
    //             return new JsonResponse('Bad request : no page number specified', Response::HTTP_BAD_REQUEST);
    //         }

    //         $moderated = $this->convertStringToBool($request->query->get("moderated"));
    //         if (!is_bool($moderated) && isset($moderated)) {
    //             return new JsonResponse('Bad request : invalid value for the parameter "moderated". Must be boolean.', Response::HTTP_BAD_REQUEST);
    //         }

    //         $token = $request->headers->get('authorization');
    //         $limit = 5;

    //         if (!$moderated) {
    //             if (isset($token)) {
    //                 $comments = $this->commentsRepository->getPage($page, $postId, $moderated);
    //             } else {
    //                 return new JsonResponse('Unauthorized : needs authentication to access the resource', Response::HTTP_UNAUTHORIZED);
    //             }
    //         } else {
    //             $comments = $this->commentsRepository->getPage($page, $postId);
    //         }

    //         $data = (object) [
    //             'data' => $comments,
    //             '_embedded' => (object)[
    //                 'page' => $page,
    //                 'limit' => $limit,
    //                 'offset' => $page * $limit,
    //                 'delivered_at' => $this->delivered_at
    //             ]
    //         ];
    //         return new JsonResponse($data);
    //     }
    //     return new JsonResponse('Method Not Allowed', 405);
    // }

    // /**
    //  * Undocumented function
    //  *
    //  * @param string $str
    //  * @return bool|string
    //  */
    // public function convertStringToBool(string $str = null)
    // {
    //     if ($str === "true") {
    //         return true;
    //     } else if ($str === "false") {
    //         return false;
    //     }
    //     return $str;
    // }
}
