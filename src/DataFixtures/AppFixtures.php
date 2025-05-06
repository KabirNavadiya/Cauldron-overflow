<?php

namespace App\DataFixtures;

use App\Entity\Answer;
use App\Entity\Question;
use App\Factory\QuestionFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        QuestionFactory::new()->createMany(10);
        QuestionFactory::new()
            ->unpublished()
            ->createMany(5);

        $answer = new Answer();
        $answer->setContent('This question is the best! I wish i knew the answer.');
        $answer->setUsername('kabir');


        $question = new Question();
        $question->setName('How to un-disappear your wallet');
        $question->setQuestion('... I should not have done this...');
        
        $answer->setQuestion($question); 

        $manager->persist($answer);
        $manager->persist($question);
        $manager->flush();

    }


}
