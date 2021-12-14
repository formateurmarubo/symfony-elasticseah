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

   
    #[Route('blog/search',methods: ['GET'], name: 'post_search')]
    public function postSearch(Request $request, PostRepository $posts)
    {
        $query = $request->query->get('q', '');
        $limit = $request->query->get('l', 10);

        if (!$request->isXmlHttpRequest()) {
            return $this->render('post/search.html.twig', ['query' => $query]);
        }
        
        try {
            $foundPosts = $posts->findBySearchQuery($query, $limit);

        } catch (\Throwable $th) {
           echo "Exception Found - " . $th->getMessage() . "<br/>";
        }
        //dd($foundPosts);
        $results = [];
        foreach ($foundPosts as $post) {
            $results[] = [
                'title' => htmlspecialchars($post->getTitle(), \ENT_COMPAT | \ENT_HTML5),
                'date' => $post->getPublishedAt()->format('M d, Y'),
                'author' => htmlspecialchars($post->getAuthor()->getFullName(), \ENT_COMPAT | \ENT_HTML5),
                'summary' => htmlspecialchars($post->getSummary(), \ENT_COMPAT | \ENT_HTML5),
                'url' => $this->generateUrl('blog_post', ['slug' => $post->getSlug()]),
            ];
        }
       // dd($this->json($results));
        return $this->json($results);
       /*
        return $this->render('post/search.html.twig', [
            'controller_name' => 'Post Search',
            'posts'=>$posts,
            'query' => $query
        ]); */
    }

    #[Route('/posts/{slug}', methods: ['GET'], name: 'blog_post')]
    public function postShow(Post $post): Response
    {
        dd($post);
        return $this->render('blog/post_show.html.twig', ['post' => $post]);
    }


}
