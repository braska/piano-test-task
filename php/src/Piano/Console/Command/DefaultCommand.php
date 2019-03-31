<?php

namespace Piano\Console\Command;

use League\Csv\Reader;
use League\Csv\Writer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Piano\Datasets;
use Piano\UserRepository;

class DefaultCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('default')
            ->addArgument('files', InputArgument::IS_ARRAY | InputArgument::REQUIRED)
            ->addOption('output', 'o', InputOption::VALUE_REQUIRED, 'Output file')
            ->addOption('key', 'k', InputOption::VALUE_REQUIRED, 'Merge key', 'user_id');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $files = $input->getArgument('files');
        $output_file = $input->getOption('output');
        $merge_key = $input->getOption('key');

        $datasets = $this->getDatasets($files);

        $result = $datasets->merge('user_id');
        $header = $result['header'];
        $records = $result['records'];

        $this->updateUids($records);

        $writer = $this->getWriter($output_file);
        $writer->insertOne($header);
        $writer->insertAll($records);

        if ($output_file) {
            $output->writeln('Done!');
        } else {
            $output->write($writer->getContent());
        }
    }

    protected function getDatasets($files)
    {
        $datasets = new Datasets();

        foreach ($files as $file) {
            $csv = Reader::createFromPath($file, 'r');
            $csv->setHeaderOffset(0);

            $header = $csv->getHeader();
            $records = $csv->getRecords();
            $datasets->add($file, $header, $records);
        }

        return $datasets;
    }

    protected function updateUids(&$records)
    {
        $user_repository = new UserRepository();
        foreach ($records as &$record) {
            $user_repository->updateUid($record);
        }
    }

    protected function getWriter($file = null)
    {
        return $file ? Writer::createFromPath($file, 'w+') : Writer::createFromString('');
    }
}
