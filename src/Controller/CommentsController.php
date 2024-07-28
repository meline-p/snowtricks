<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Form\CommentsFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Controller class responsible for handling the addition of a new comment to a trick.
 */
#[Route('/commentaires', name: 'app_comments_')]
class CommentsController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * Handles the addition of a new comment to a trick.
     *
     * @param Request       $request the HTTP request object containing form data and headers
     * @param Trick         $trick   the Trick entity to which the comment will be added
     * @param UserInterface $user    the current authenticated user who is adding the comment
     *
     * @return Response a Response object which could be a redirect to the previous page with the comments section,
     *                  or a rendered view with the comment form
     */
    #[Route('/ajouter/{trick}', name: 'add', requirements: ['trick' => '[a-z0-9\-]+'])]
    public function add(Request $request, Trick $trick, UserInterface $user): Response
    {
        $comment = new Comment();
        $commentForm = $this->createForm(CommentsFormType::class, $comment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setContent($comment->getContent());
            $comment->setUser($user);
            $comment->setTrick($trick);

            $this->em->persist($comment);
            $this->em->flush();

            $route = $request->headers->get('referer');

            return $this->redirect($route.'#comments');
        }

        return $this->render('comments/add.html.twig', [
            'commentForm' => $commentForm->createView(),
        ]);
    }
}
