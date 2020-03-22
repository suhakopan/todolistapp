<?php

namespace App\Controller;

use App\Entity\Constants;
use App\Entity\Developer;
use App\Entity\Task;
use App\Entity\Temporary;
use App\Entity\Timesheet;
use App\Service\GetTasks;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Time;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     * @return Response
     */
    public function index()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $taskRepository = $this->getDoctrine()->getRepository(Task::class);
        $tasks = $taskRepository->findAll();
        $devs = $this->developerCount();
        $isFinish = $this->alltasksDone();
        $totalDuration = 0;
        $week = 1;
        while($isFinish != true)
        {
            foreach ($tasks as $task)
            {
                if($totalDuration == ($devs * Constants::$weeklyHour)) //45*5=225
                {
                    //yeni haftaya geç
                    $week++;
                    $totalDuration = 0;
                    $tempRepository = $this->getDoctrine()->getRepository(Temporary::class);
                    $temps = $tempRepository->findAll();
                    foreach ($temps as $t)
                    {
                        $timesheet = new Timesheet();
                        $timesheet->setWeek($t->getWeek());
                        $timesheet->setDuration($t->getDuration());
                        $timesheet->setDevName($t->getDevName());
                        $timesheet->setTaskName($t->getTaskName());
                        $entityManager->persist($timesheet);
                        $totalDuration+=$t->getDuration();
                    }
                    $entityManager->flush();
                    $this->clearTemp();
                    //yeni haftayı doldur
                    $timesheet = new Timesheet();
                    $timesheet->setTaskName($task->getDescription());
                    $timesheet->setDevName($this->getDevName($task->getLevel()));
                    if($this->getDevTotalHourWeeks($this->getDevName($task->getLevel()),$week) < 45)
                    {
                        if(($this->getDevTotalHourWeeks($this->getDevName($task->getLevel()),$week) + $task->getDuration()) > Constants::$weeklyHour)
                        {
                            //Yeni haftaya sarkacak
                            $part1 = Constants::$weeklyHour - $this->getDevTotalHourWeeks($this->getDevName($task->getLevel()),$week);
                            $part2 = ($task->getDuration() + $this->getDevTotalHourWeeks($this->getDevName($task->getLevel()),$week)) - Constants::$weeklyHour;
                            $timesheet->setDuration($part1);
                            $timesheet->setWeek($week);
                            //artanı geçici tabloya at
                            $temporary = new Temporary();
                            $temporary->setTaskName($task->getDescription());
                            $temporary->setDevName($this->getDevName($task->getLevel()));
                            $temporary->setDuration($part2);
                            $temporary->setWeek($week+1);

                            $entityManager->persist($timesheet);
                            $entityManager->persist($temporary);

                            $totalDuration += $part1;
                        }
                        else //sarkma yoksa
                        {
                            $timesheet->setDuration($task->getDuration());
                            $timesheet->setWeek($week);
                            $entityManager->persist($timesheet);
                            $totalDuration += $task->getDuration();
                        }
                        $entityManager->flush();
                    }
                    $this->updateTask($task->getId());
                }
                else
                {
                    $timesheet = new Timesheet();
                    $timesheet->setTaskName($task->getDescription());
                    $timesheet->setDevName($this->getDevName($task->getLevel()));
                    if($this->getDevTotalHourWeeks($this->getDevName($task->getLevel()),$week) < 45)
                    {
                        if(($this->getDevTotalHourWeeks($this->getDevName($task->getLevel()),$week) + $task->getDuration()) > Constants::$weeklyHour)
                        {
                            //Yeni haftaya sarkacak
                            $part1 = Constants::$weeklyHour - $this->getDevTotalHourWeeks($this->getDevName($task->getLevel()),$week);
                            $part2 = ($task->getDuration() + $this->getDevTotalHourWeeks($this->getDevName($task->getLevel()),$week)) - Constants::$weeklyHour;
                            $timesheet->setDuration($part1);
                            $timesheet->setWeek($week);
                            //artanı geçici tabloya at
                            $temporary = new Temporary();
                            $temporary->setTaskName($task->getDescription());
                            $temporary->setDevName($this->getDevName($task->getLevel()));
                            $temporary->setDuration($part2);
                            $temporary->setWeek($week+1);

                            $entityManager->persist($timesheet);
                            $entityManager->persist($temporary);

                            $totalDuration += $part1;
                        }
                        else //sarkma yoksa
                        {
                            $timesheet->setDuration($task->getDuration());
                            $timesheet->setWeek($week);
                            $entityManager->persist($timesheet);
                            $totalDuration += $task->getDuration();
                        }
                        $entityManager->flush();
                    }
                    $this->updateTask($task->getId());
                }
            }
            $isFinish = $this->alltasksDone();
        }
        $totalWeek = $this->getTotalWeek();
        return $this->render('home/index.html.twig', [
            'isFinish' => $isFinish,
            'week' => $totalWeek
        ]);
    }

    /**
     * @Route("/home/{week}", name="home_show")
     * @return Response
     */
    public function show($week)
    {
        $em = $this->getDoctrine()->getManager();
        $sql = 'SELECT * FROM timesheet WHERE week = :week';
        $stt = $em->getConnection()->prepare($sql);
        $stt->bindValue('week', $week);
        $stt->execute();
        $result = $stt->fetchAll();
        return $this->render('home/show.html.twig', [
            'timesheets' => $result
        ]);
    }
    public function updateTask($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $taskRepository = $entityManager->getRepository(Task::class);
        $task = $taskRepository->find($id);
        $task->setIsDone(true);
        $entityManager->persist($task);
        $entityManager->flush();
    }

    public function isEmpty($id)
    {
        $em = $this->getDoctrine()->getManager();
        $sql = 'SELECT count(*) FROM task WHERE level = :id and is_done = false';
        $stt = $em->getConnection()->prepare($sql);
        $stt->bindValue('id', $id);
        $stt->execute();
        $result = $stt->fetchAll();
        if($result[0]['count(*)'] == 0)
            return true;
        else
            return false;
    }

    public function alltasksDone()
    {
        $em = $this->getDoctrine()->getManager();
        $sql = 'SELECT count(*) FROM task WHERE is_done = false';
        $stt = $em->getConnection()->prepare($sql);
        $stt->execute();
        $result = $stt->fetchAll();
        if($result[0]['count(*)'] == 0)
            return true;
        else
            return false;
    }

    public function developerCount()
    {
        $em = $this->getDoctrine()->getManager();
        $sql = 'SELECT count(*) FROM developer';
        $stt = $em->getConnection()->prepare($sql);
        $stt->execute();
        $result = $stt->fetchAll();
        return $result[0]['count(*)'];
    }

    public function getDevName($level)
    {
        $em = $this->getDoctrine()->getManager();
        $sql = 'SELECT name FROM developer WHERE experience= :exp';
        $stt = $em->getConnection()->prepare($sql);
        $stt->bindValue('exp', $level);
        $stt->execute();
        $result = $stt->fetchAll();
        return $result[0]['name'];
    }

    public function getDevTotalHourWeeks($dev,$week)
    {
        $em = $this->getDoctrine()->getManager();
        $sql = 'SELECT sum(duration) as total FROM timesheet WHERE dev_name= :dev and week= :week';
        $stt = $em->getConnection()->prepare($sql);
        $stt->bindValue('dev', $dev);
        $stt->bindValue('week', $week);
        $stt->execute();
        $result = $stt->fetchAll();
        return $result[0]['total'];
    }

    public function clearTemp()
    {
        $em = $this->getDoctrine()->getManager();
        $sql = 'DELETE FROM temporary';
        $stt = $em->getConnection()->prepare($sql);
        $stt->execute();
    }

    public function getTotalWeek()
    {
        $em = $this->getDoctrine()->getManager();
        $sql = 'SELECT week FROM timesheet ORDER BY id DESC limit 1';
        $stt = $em->getConnection()->prepare($sql);
        $stt->execute();
        $result = $stt->fetchAll();
        return $result[0]['week'];
    }
}