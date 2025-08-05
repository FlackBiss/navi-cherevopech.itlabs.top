<?php

namespace App\Command;

use App\Service\MeilisearchService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:meilisearch:index',
    description: 'Индексировать данные Tenant',
)]
class MeilisearchIndexCommand extends Command
{
    private MeilisearchService $meilisearchService;

    public function __construct(MeilisearchService $meilisearchService)
    {
        parent::__construct();
        $this->meilisearchService = $meilisearchService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->writeln('Начинаем индексацию Tenant...');
        $this->meilisearchService->indexTenants();
        $io->writeln('Tenant успешно проиндексированы.');

        $io->success('Индексация завершена.');

        return Command::SUCCESS;
    }
}
