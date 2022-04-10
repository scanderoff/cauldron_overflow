<?php

namespace App\Controller;

use App\Service\MarkdownHelper;
use App\Entity\Question;
use App\Repository\QuestionRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class QuestionController extends AbstractController
{
    private $logger;
    private $isDebug;

    public function __construct(LoggerInterface $logger, bool $isDebug)
    {
        $this->logger = $logger;
        $this->isDebug = $isDebug;
    }


    /**
     * @Route("/", name="app_homepage")
     */
    public function homepage(QuestionRepository $repository)
    {
        $questions = $repository->findAllAskedOrderedByNewest();

        return $this->render('question/homepage.html.twig', [
            'questions' => $questions,
        ]);
    }

    /**
     * @Route("/questions/new")
     */
    public function new(EntityManagerInterface $entityManager): Response
    {
        return new Response('Sounds like a great feature for V2!');
    }

    /**
     * @Route("/questions/{slug}", name="app_question_show")
     */
    public function show(Question $question)
    {
        if ($this->isDebug)
            $this->logger->info('We are in debug mode!');

        // $answers = $question->getAnswers();
        // в этот момент запрос на получение ответов еще не отправлен

        // ленивая загрузка (lazy loading)
        // ответы загружаются из бд только после того,
        // как мы к ним обращаемся в шаблоне
        return $this->render('question/show.html.twig', [
            'question' => $question,
            // 'answers' => $answers,
            // закомментил, т.к. мы можем получить ответы из объекта $question
            // внутри шаблона - question.answers. Вызовется $question->getAnswers()
        ]);
    }

    /**
     * @Route("questions/{slug}/vote", name="app_question_vote", methods="POST")
     */
    public function questionVote(Question $question, Request $request, EntityManagerInterface $entityManager)
    {
        $direction = $request->request->get('direction');

        if ($direction === 'up')
            $question->upVote();
        elseif ($direction === 'down')
            $question->downVote();

        $entityManager->flush();

        return $this->redirectToRoute('app_question_show', [
            'slug' => $question->getSlug(),
        ]);
    }
}
