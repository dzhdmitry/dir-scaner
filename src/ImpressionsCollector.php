<?php

namespace Scanner;

use Scanner\DataTransfer\Impression;
use Scanner\DataTransfer\Money;
use Scanner\DataTransfer\Row;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validation;

class ImpressionsCollector
{
    /**
     * @var SymfonyStyle
     */
    private $io;

    public function __construct(SymfonyStyle $io)
    {
        $this->io = $io;
    }

    /**
     * @param string $directory
     * @return Impression[]
     */
    public function collect(string $directory): array
    {
        /** @var Impression[] $impressionsByHash */
        $impressionsByHash = [];

        foreach ($this->getImpressionsGenerator($directory) as $impression) {
            $hash = $impression->getHashKey();

            if (array_key_exists($hash, $impressionsByHash)) {
                $impressionsByHash[$hash]->add($impression);
            } else {
                $impressionsByHash[$hash] = $impression;
            }
        }

        return $impressionsByHash;
    }

    /**
     * @param string $directory
     * @return \Generator|Impression[]
     */
    private function getImpressionsGenerator(string $directory)
    {
        $iterator = $this->createIterator($directory);
        $validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->getValidator();

        foreach ($iterator as $filename) {
            if (pathinfo($filename, PATHINFO_EXTENSION) !== 'csv') {
                continue;
            }

            if (($handle = fopen($filename, 'row')) !== false) {
                $i = 0;

                while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                    $i++;

                    if (count($data) !== 5) {
                        $this->invalidDataMessage('File must contain 5 columns', $filename);

                        break;
                    }

                    if ($i === 1) {
                        if ($data !== ['date', 'geo', 'zone', 'impressions', 'revenue']) {
                            $this->invalidDataMessage('File has wrong columns', $filename);

                            break;
                        }

                        continue;
                    }

                    $row = (new Row())
                        ->setDate($data[0])
                        ->setGeo($data[1])
                        ->setZone($data[2])
                        ->setImpressions($data[3])
                        ->setRevenue($data[4])
                    ;

                    $errors = $validator->validate($row);

                    if (count($errors) !== 0) {
                        $errorText = [];

                        foreach ($errors as $violation) {
                            /** @var ConstraintViolation $violation */
                            $errorText[] = "\n" . $violation->getPropertyPath() . ' - ' . $violation->getMessage();
                        }

                        $this->invalidDataMessage(implode('', $errorText), $filename);

                        continue;
                    }

                    try {
                        $impression = (new Impression())
                            ->setDate(new \DateTime($row->getDate()))
                            ->setGeo($row->getGeo())
                            ->setZone(intval($row->getZone()))
                            ->setImpressions(intval($row->getImpressions()))
                            ->setRevenue(Money::fromString($row->getRevenue()));
                    } catch (\Exception $e) {
                        $this->invalidDataMessage('Cannot convert date', $filename);

                        continue;
                    }

                    yield $impression;
                }

                fclose($handle);
            }
        }
    }

    /**
     * @param string $message
     * @param string $filename
     */
    private function invalidDataMessage(string $message, string $filename)
    {
        $this->io->note(sprintf(
            'Invalid data at file %s: %s',
            $filename,
            $message
        ));
    }

    /**
     * @param string $directory
     * @return \RecursiveIteratorIterator
     */
    private function createIterator(string $directory): \RecursiveIteratorIterator
    {
        $iterator = new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS);

        return new \RecursiveIteratorIterator($iterator);
    }
}
