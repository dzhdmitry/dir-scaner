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
        $directory = $input->getArgument('directory');

        if (!is_dir($directory)) {
            $io->error('Directory does not exists');

            return null;
        }

        $collector = new ImpressionsCollector($io);
        $db = new DatabaseConnection(
            getenv('DATABASE_HOST'),
            getenv('DATABASE_PORT'),
            getenv('DATABASE_NAME'),
            getenv('DATABASE_USER'),
            getenv('DATABASE_PASSWORD')
        );

        $start = $db->prepare('START TRANSACTION');
        $commit = $db->prepare('COMMIT');
        $rollback = $db->prepare('ROLLBACK');

        $select = $db->prepare('SELECT id FROM impressions 
            WHERE date = :date AND geo = :geo AND zone = :zone
            FOR UPDATE 
            LIMIT 1');

        $update = $db->prepare('UPDATE impressions
            SET impressions = impressions + :impressions, revenue = revenue + :revenue 
            WHERE id = :id');

        $insert = $db->prepare('INSERT INTO impressions
            (date, geo, zone, impressions, revenue)
            VALUES (:date, :geo, :zone, :impressions, :revenue)');

        foreach ($collector->collect($directory) as $impression) {
            $start->execute();

            try {
                $select->execute([
                    'date' => $impression->getDate()->format('Y-m-d'),
                    'geo' => $impression->getGeo(),
                    'zone' => $impression->getZone()
                ]);

                $existing = $select->fetchAll(\PDO::FETCH_COLUMN);

                if (count($existing) !== 0) {
                    $update->execute([
                        'id' => $existing[0],
                        'impressions' => $impression->getImpressions(),
                        'revenue' => $impression->getRevenue()->getValue()
                    ]);
                } else {
                    $insert->execute([
                        'date' => $impression->getDate()->format('Y-m-d'),
                        'geo' => $impression->getGeo(),
                        'zone' => $impression->getZone(),
                        'impressions' => $impression->getImpressions(),
                        'revenue' => $impression->getRevenue()->getValue()
                    ]);
                }

                $commit->execute();
            } catch (\PDOException $e) {
                $rollback->execute();
            }
        }

        $io->success('Scanning finished');

        return 0;
    }
}
