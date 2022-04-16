<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Repository\AnswerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class AnswerController extends BaseController
{
    /**
     * @Route("/answers/{id}/vote", name="answer_vote", methods="POST")
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     */
    public function answerVote(Answer $answer, LoggerInterface $logger, Request $request, EntityManagerInterface $entityManager)
    {
        $logger->info('{user} is voting on answer {answer}', [
            'user' => $this->getUser()->getEmail(),
            'answer' => $answer->getId(),
        ]);

        $data = json_decode($request->getContent(), true)['data'];
        $direction = $data['direction'] ?? 'up';

        if ($direction === 'up')
        {
            $logger->info('Voting up!');
            $answer->setVotes($answer->getVotes() + 1);
        }
        elseif ($direction === 'down')
        {
            $logger->info('Voting down!');
            $answer->setVotes($answer->getVotes() - 1);
        }

        $entityManager->flush();

        return $this->json(['votes' => $answer->getVotes()]);
    }

    /**
     * @Route("/answers/popular", name="app_popular_answers")
     */
    public function popularAnswers(Request $request, AnswerRepository $answerRepository)
    {
        $answers = $answerRepository->findMostPopular(
            $request->query->get('q'),
        );

        return $this->render('answer/popularAnswers.html.twig', [
            'answers' => $answers,
        ]);
    }
}
