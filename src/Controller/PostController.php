<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    #[Route('/post',methods: ['GET', 'POST'], name: 'post_new')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
         $post = new Post;

         $form = $this->createForm(PostType::class, $post)
             ->add("saveAndCreateNew", SubmitType::class);

        $form->handleRequest($request);
          //dd($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $date = new \DateTime("now", new \DateTimeZone("Europe/Paris"));
            $post->setPublishedAt($date);

           // dd($post);
            $entityManager->persist($post);
            $entityManager->flush();
            $this->addFlash('success', 'Post.created_successfully');

            if ($form->get('saveAndCreateNew')->isClicked()) {
                return $this->redirectToRoute('post_new');
            }

        }

        return $this->render('post/index.html.twig', [
            'controller_name' => 'Post New',
            'form'=>$form->createView(),
            'post'=>$post
        ]);
    }

    #[Route('/post/listing',methods: ['GET'], name: 'post_listing')]
    public function listingPosts(PostRepository $repoPost)
    {
        try {
            $posts= $repoPost->findAll();
        } catch (\Throwable $th) {
            echo "Exception Found - " . $th->getMessage() . "<br/>";
        }

        return $this->render('post/listing.html.twig', [
            'controller_name' => 'Post Listing',
            'posts'=>$posts
        ]);
    }



}
