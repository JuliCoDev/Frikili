<?php

namespace App\Controller;
use App\Entity\Posts;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class DashBoradController extends AbstractController
{
    /**
     * @Route("/dashborad", name="dash_borad")
     */
    public function index(PaginatorInterface $paginator, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository(Posts::class)->BuscarTodosLosPosts();
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            1 /*limit per page*/
        );
        return $this->render('dash_borad/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }
}
