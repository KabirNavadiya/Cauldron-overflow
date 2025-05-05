<?php

namespace App\Controller;

use App\Entity\Question;
use App\Repository\QuestionRepository;
use App\Service\MarkdownHelper;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

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
     * @Route("/questions/newquestion")
     */
    public function newquestion(EntityManagerInterface $entitymanager)
    {
       

        return new Response('Sounds like a GREAT feature for V2');
    }

    /**
     * @Route("/questions/{slug}", name="app_question_show")
     */

    public function show(Question $question)
    {
        if ($this->isDebug) {
            $this->logger->info('We are in debug mode!');
        }

        // $repository = $entitymanager->getRepository(Question::class);

        // /**
        //  * @var Question|null $question
        //  */

        // $question = $repository->findOneBy(['slug'=>$slug]);

        // if(!$question){
        //     throw $this->createNotFoundException(sprintf('No question found for slug "%s"',$slug));
        // } 


        $answers = [
            'Make sure your cat is sitting `purrrfectly` still 🤣',
            'Honestly, I like furry shoes better than MY cat',
            'Maybe... try saying the spell backwards?',
        ];


        return $this->render('question/show.html.twig', [
            'question' => $question,
            'answers' => $answers,
        ]);
    }

    /** 
     * @Route("/questions/{slug}/vote",name="app_question_vote",methods="POST")
     */
    public function questionVote(Question $question, Request $request, EntityManagerInterface $entitymanager)
    {
        $direction = $request->request->get('direction');
        if ($direction === 'up') {
            $question->upVote();
        } elseif ($direction === 'down') {
            $question->downVote();
        }

        $entitymanager->flush();

        return $this->redirectToRoute('app_question_show',[
            'slug'=> $question->getSlug(),
        ]);

    }
    
}
