<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Posts;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use App\Form\PostsType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class PostsController extends AbstractController
{
    /**
     * @Route("/registrar-posts", name="RegistrarPosts")
     */
    public function index(Request $request  , SluggerInterface $slugger)
    {
        $post = new Posts();
        $form = $this->createForm(PostsType::class, $post);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $brochureFile = $form->get('foto')->getData();
            if ($brochureFile)
            {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('photos_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new \Exception('Ups ha ocurrido un error');
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $post->setFoto($newFilename);
            }
            $user = $this->getUser();
            $post->setUserId($user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();
            return $this->redirectToRoute('dash_borad');
        }

        return $this->render('posts/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
