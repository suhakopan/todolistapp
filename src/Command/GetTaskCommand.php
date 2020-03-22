<?php

namespace App\Command;

use App\Service\GetTasks;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GetTaskCommand extends Command
{
    private $task;

    public function __construct(GetTasks $tasks)
    {
        $this->task = $tasks;
        parent::__construct();

    }

    protected function configure()
    {
        $this
            ->setName('app:getTask')
            ->setDescription('Get tasks from providers and insert the database')
            ->setHelp('Bu komut ile providerın gönderdiği görevleri veritabanına kaydedebilirsiniz.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->task->insertTasksDatabase();
        $io = new SymfonyStyle($input,$output);
        $io->success('Başarılı!','Tüm görevler veritabanına kaydedildi.');
    }
}
