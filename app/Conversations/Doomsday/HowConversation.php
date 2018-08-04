<?php

namespace App\Conversations\Doomsday;

use App\User;
use Validator;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;

class HowConversation extends Conversation
{
    protected $bot;
    protected $telegram;
    protected $user;

    /**
     * Ask how the user wishes to receive the dommsday alert
     */
    private function askHow()
    {
        $question = Question::create('De que maneira você gostaria de receber este alerta?')
            ->fallback('Unable to ask question')
            ->callbackId('ask_reason')
            ->addButtons([
                Button::create('Aqui')->value('telegram'),
                Button::create('E-mail')->value('email'),
            ]);

        return $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                switch($answer->getValue()) {
                    case 'telegram':
                        $this->user->update(['doomsday_alert' => 'telegram']);
                        $this->say('Beleza!');
                        $this->askHowFarInAdvance();
                        break;
                    case 'email':
                        $this->say('Legal!');
                        $this->askEmail();
                        break;
                }
            } else {
                $this->say('Não entendi... :-(');
                $this->askHow();
            }
        });
    }

    private function askEmail()
    {
        $question = Question::create('Qual é o seu e-mail?')
            ->fallback('Unable to ask question')
            ->callbackId('ask_reason');

        return $this->ask($question, function (Answer $answer) {
            $email = $answer->getText();
            \Log::debug($email);
            $this->say($email);
            $validator = Validator::make(
                ['email' => $email],
                ['email' => 'email']
            );

            if ($validator->passes()) {
                $alreadyInUse = User::whereEmail($email)
                    ->where('telegram_id', '<>', $this->user->telegram_id)
                    ->first();

                if ($alreadyInUse) {
                    $this->say('Olha, um outro usuário já cadastrou esse e-mail...');
                    $this->askEmail();
                } else {
                    $this->user->update([
                        'email' => $email,
                        'doomsday_alert' => 'email'
                    ]);
                    $this->say('Obrigado! Guardei seu e-mail aqui ;-)');
                    $this->askHowFarInAdvance();
                }
            } else {
                $this->say('Preciso de um e-mail válido :-/');
                $this->askEmail();
            }
        });
    }

    private function askHowFarInAdvance()
    {
        $question = Question::create('Com quanta antecedência você gostaria de receber este alerta?')
            ->fallback('Unable to ask question')
            ->callbackId('ask_reason')
            ->addButtons([
                Button::create('1 dia')->value(1),
                Button::create('3 dias')->value(3),
                Button::create('1 semana')->value(7),
            ]);

        return $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $this->user->update(['doomsday_advance' => $answer->getValue()]);
                $this->say('Então fechou!');
                $this->say('Todos os dias vou verificar se tem alguma chance do mundo acabar devido a uma colisão com um meteoro, e te aviso com a antecedência que você pediu.');
                $this->say('Agora é só esperar os alertas :-)');
            } else {
                $this->say('Não entendi... :-(');
                $this->askHowFarInAdvance();
            }
        });
    }


    /**
     * Start the conversation
     */
    public function run()
    {
        $this->bot       = $this->getBot();
        $this->telegram  = $this->bot->getUser();

        $this->user = User::firstOrCreate(
            ['telegram_id' => $this->telegram->getId()],
            [
                'name'     => $this->telegram->getFirstName() . ' ' . $this->telegram->getLastName(),
                'email'    => $this->telegram->getId() . '@undefined.com',
                'info'     => json_encode($this->telegram->getInfo()),
                'password' => bcrypt($this->telegram->getId()),
            ]
        );

        $this->say('É pra já!');
        $this->askHow();
    }
}
