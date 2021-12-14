<?php
namespace App\Elasticsearch;
use App\Entity\Post;
use App\Repository\PostRepository;
use Elastica\Client;
use Elastica\Document;
use Elastica\Type;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ArticleIndexer 
{
    private $client;
    private $postRepository;
    private $router;

    public function __construct(Client $client, PostRepository $postRepository, UrlGeneratorInterface $router)
    {
        $this->client = $client;
        $this->postRepository = $postRepository;
        $this->router = $router;
    }

    public function buildDocument(Post $post)
    {
        return new Document(
            $post->getId(), //get id manually
            [
              'title' => $post->getTitle(),
              'summary' => $post->getSummary(),
              'author' => $post->getAuthor()->getFullName(),
              'content' => $post->getContent(),
              // Not indexed but needed for display
              'url' => $this->router->generate('blog_post', ['slug' => $post->getSlug()], UrlGeneratorInterface::ABSOLUTE_PATH),
              'date' => $post->getPublishedAt()->format('M d, Y'),
            ],
            'blog',
        );
 
    }

    public function indexAllDocuments($indexName)
    { 

       

        $allPosts = $this->postRepository->findAll();
       
        $index = $this->client->getIndex($indexName);
        //$type = $index->getType($indexName);
        //dd($type);
        $documents = [];
        foreach ($allPosts as $post) {
            $documents[] = $this->buildDocument($post);
        }
        
       // dd($documents);
        $index->addDocuments($documents);
        //$index->refresh();
       // dd($index->addDocuments($documents));
        $index->refresh();

    }
   
}