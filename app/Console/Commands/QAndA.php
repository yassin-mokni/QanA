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
                ['Add questions and answers', 'View Questions', '<- exit']
            );

            switch ($choice0) {
                case 'Add questions and answers':
                    $this->addQuestions();
                    break;
                case 'View Questions':
                    $this->viewQuestions();
                    break;
                default:
                    # code...
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
    public function viewQuestions()
    {
        $choice1 = null;
        //get list of questions
        $questions = Question::get();
        if (empty($questions->count())) {
            $this->error('No Questions Yet !');
            $choice1 = '<-Back';
        }
        while ($choice1 != '<-Back') {
            //counting answered questions to fill the progress
            $answeredQuestionsCount = $questions->filter(fn($q) => $q->answered)->count();

            $this->info('Your Current Progress : ');
            $this->newLine();
            $bar = $this->output->createProgressBar($questions->count());
            $bar->start();
            //wait 1 second for the progress bar to be filled
            sleep(1);
            $bar->advance($answeredQuestionsCount);
            $this->newLine(2);

            $choice1 = $this->choice(
                'Please choose a question to practice',
                [...$questions->pluck('content')->toArray(), '<- Back']
            );

            if ($choice1 != '<- Back') {
                //get question answers
                $userQuestionAnswer = $this->ask($choice1);
                $rightQuestionAnswer = $questions->filter(fn($q) => $q->content == $choice1)->pop();
                if ($userQuestionAnswer === $rightQuestionAnswer->answer->content) {
                    //increment score
                    $rightQuestionAnswer->answered = 1;
                    $rightQuestionAnswer->save();
                    $questions->fresh();
                }
            }

        }
    }
}
