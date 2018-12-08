<?php

namespace Scanner;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ScanCommand extends Command
{
    protected function configure()
    {
        $this->addArgument('directory', InputArgument::REQUIRED, 'Scanning directory');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $db = new DatabaseConnection('postgres', '5432', 'scanner', 'postgres', null);
        $directory = $input->getArgument('directory');

        if (!is_dir($directory)) {
            $io->error('Directory does not exists');

            return null;
        }

        $collector = new ImpressionsCollector($io, $directory);
        $repository = new ImpressionRepository($db);

        foreach ($collector->collect() as $impression) {
            $statement = $db->prepare('START TRANSACTION');
            $statement->execute();

            try {
                $existing = $repository->find($impression->getDate(), $impression->getGeo(), $impression->getZone());

                if ($existing) {
                    $repository->update(
                        $existing['id'],
                        $existing['impressions'] + $impression->getImpressions(),
                        floatval($existing['revenue']) + $impression->getRevenue()
                    );
                } else {
                    $repository->insert(
                        $impression->getDate(),
                        $impression->getGeo(),
                        $impression->getZone(),
                        $impression->getImpressions(),
                        $impression->getRevenue()
                    );
                }

                $statement = $db->prepare('COMMIT');
                $statement->execute();
            } catch (\PDOException $e) {
                $statement = $db->prepare('ROLLBACK');
                $statement->execute();
            }
        }

        $io->success('Scanning finished');

        return 0;
    }
}
