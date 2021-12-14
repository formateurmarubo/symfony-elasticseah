<?php

namespace App\Command;

use App\Elasticsearch\ArticleIndexer;
use App\Elasticsearch\IndexBuilder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'elastic:reindex',
    description: 'commande Symfony qui crée l\'index et l\'alimenter avec Elastica',
)]
class ElasticReindexCommand extends Command
{
    protected static $defaultName = 'elastic:reindex';
    private $indexBuilder;
    private $articleIndexer;

    public function __construct(IndexBuilder $indexBuilder, ArticleIndexer $articleIndexer)
    {
        $this->indexBuilder = $indexBuilder;
        $this->articleIndexer = $articleIndexer;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
           ->setDescription('Rebuild the Index and populate it.');
        /*   
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;*/
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $io = new SymfonyStyle($input, $output);

        $index = $this->indexBuilder->create();
        $io->success('Index created!');
        $this->articleIndexer->indexAllDocuments($index->getName());
        $io->success('Index populated and ready!');
        return Command::SUCCESS;
        /*
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
        */
    }
}
