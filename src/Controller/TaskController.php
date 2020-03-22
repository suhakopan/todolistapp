<?php

namespace App\Controller;

use App\Entity\Constants;
use App\Entity\Task;
use App\Entity\Urun;
use App\Service\GetTasks;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    /**
     * @Route("/get/tasks", name="task")
     */
    public function index(GetTasks $getTasks)
    {
        $provider = new Constants();
        $providerList = $provider->getProviders();
        $content = [];
        $entityManager = $this->getDoctrine()->getManager();

        foreach ($providerList as $p)
        {
            $content = $getTasks->getTasksFromProviders($p);
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
        return new Response(sprintf('All tasks received and inserted database.'));
    }

    /**
     * @Route("/all/tasks", name="all_tasks")
     * @return Response
     */
    public function show()
    {
        $taskRepository = $this->getDoctrine()->getRepository(Task::class);
        $tasks = $taskRepository->findAll();
        return $this->render('task/index.html.twig', [
            'content' => $tasks,
        ]);
    }
}
