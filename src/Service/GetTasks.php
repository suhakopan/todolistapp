<?php

namespace App\Service;

use App\Entity\Constants;
use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;

class GetTasks extends AbstractController
{
    public function getTasksFromProviders(string $url)
    {
        $client = HttpClient::create();
        $request= $client->request('GET',$url);
        $list = $request->toArray();
        return $list;
    }

    public function insertTasksDatabase()
    {
        $provider = new Constants();
        $providerList = $provider->getProviders();
        $content = [];
        $entityManager = $this->getDoctrine()->getManager();
        foreach ($providerList as $p)
        {
            $content = $this->getTasksFromProviders($p);
            foreach ($content as $c)
            {
                if(array_key_first($c) != 'zorluk') {
                    $task = new Task();
                    $desc = array_key_first($c);
                    $task->setDescription($desc);
                    $task->setDuration($c[$desc]['estimated_duration']);
                    $task->setLevel($c[$desc]['level']);
                    $task->setIsDone(false);
                    $entityManager->persist($task);
                    $entityManager->flush();
                } else {
                    $task = new Task();
                    $task->setDescription($c['id']);
                    $task->setDuration($c['sure']);
                    $task->setLevel($c['zorluk']);
                    $task->setIsDone(false);
                    $entityManager->persist($task);
                    $entityManager->flush();
                }
            }
        }
    }
}