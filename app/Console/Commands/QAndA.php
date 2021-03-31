<?php

namespace App\Console\Commands;

use App\Answer;
use App\Question;
use Illuminate\Console\Command;

class QAndA extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qanda:interactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs an interactive command line based Q And A system.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        do {
            $choice0 = $this->choice(
                'Please choose a Q and A option',
                ['Add questions and answers', 'Practice Questions', '<- exit']
            );

            switch ($choice0) {
                case 'Add questions and answers':
                    $this->addQuestions();
                    break;
                case 'Practice Questions':
                    $this->practiceQuestions();
                    break;
                default:
                    break;
            }
        } while ($choice0 != '<- exit');
    }

    public function addQuestions()
    {
        do {
            //validation
            do {
                $questionContent = $this->ask('Write the question');
            } while ($questionContent == '');
            do {
                $answerContent = $this->ask('Write the answer');
            } while ($answerContent == '');

            //save in db
            $question = new Question;
            $question->content = $questionContent;
            $question->save();

            $answer = new Answer;
            $answer->content = $answerContent;
            $question->answer()->save($answer);
            //ask if he want to continue
            $moreQuestions = $this->confirm('Do you want to add another question?');

        } while ($moreQuestions == 'yes');
    }
    public function practiceQuestions()
    {
        $choice1 = null;
        //get list of questions
        $questions = Question::get();
        if (empty($questions->count())) {
            $this->error('No Questions Yet !');
            $choice1 = '<- Back';
        }
        while ($choice1 != '<- Back') {
            //counting answered questions to fill the progress
            $answeredQuestionsCount = $questions->filter(fn($q) => $q->answered)->count();

            $this->showProgress($questions->count(), $answeredQuestionsCount);

            $this->showFinalProgress($questions, $answeredQuestionsCount);

            $choice1 = $this->choice(
                'Please choose a question to practice',
                [...$questions->pluck('content')->toArray(), '<- Back']
            );

            if ($choice1 != '<- Back') {
                //save user answer
                $userQuestionAnswer = $this->ask($choice1);
                $rightQuestionAnswer = $questions->filter(fn($q) => $q->content == $choice1)->pop();

                //save that the user has answered the question
                $rightQuestionAnswer->answered = 1;

                //increment score if correct
                if ($userQuestionAnswer === $rightQuestionAnswer->answer->content) {
                    $rightQuestionAnswer->is_correct = 1;
                }
                $rightQuestionAnswer->save();
                $questions->fresh();
            }

        }
    }

    public function showProgress(int $count = 0, int $answeredQuestionsCount = 0)
    {
        $this->info('Your Current Progress : ');
        $this->newLine();
        $bar = $this->output->createProgressBar($count);
        $bar->start();
        //wait 1 second for the progress bar to be filled
        if ($answeredQuestionsCount > 0) {
            sleep(1);
            $bar->advance($answeredQuestionsCount);
        }

        $this->newLine(2);
    }

    public function showFinalProgress(iterable $questions, int $answeredQuestionsCount = 0)
    {
        //check if he completed the questions
        if ($answeredQuestionsCount == $questions->count()) {
            $correctQuestionsCount = $questions->filter(fn($q) => $q->is_correct)->count();
            $this->info('And Your final Progress : ' . $correctQuestionsCount . " / " . $questions->count());
            $this->newLine();
        }
    }
}
