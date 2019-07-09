<?php namespace Modules\User\Console;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Contracts\Mail\Mailer;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Modules\User\Repositories\UserRepository;
use Modules\User\Events\NotifyExpertForMessage;
use Modules\User\Repositories\ChatMessageRepository;
use Carbon\Carbon;

class ExpertReminderEmailCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
     protected $name = 'expert:notify-expert-for-message {interval}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify expert on interval elapsed since last message received';

    /**
     * @var User
     */
    public $trader;
    public $expert;
    public $chatMessage;

    /**
     * @var Mailer
     */
    public $mail;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
      Mailer $mail,
      ChatMessageRepository $chatMessage,
      UserRepository $userRepository
      )
    {
        parent::__construct();

        $this->mail        = $mail;
        $this->chatMessageRepo = $chatMessage;
        $this->userRepository = $userRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        \Log::info('Running ExpertReminderEmailCommand');
        $interval = $this->argument('interval');

        $meta = [];
        $chatUsers  = [];

        if($interval) {
            $messages = $this->chatMessageRepo->filterUsing($meta)
                                          ->select('id', 'user_id', 'chatable_id', 'notified', 'created_at', 'notified')
                                          ->orderBy('id', 'desc');

            $intervalMessages = clone $messages;
            $intervalMessages = $intervalMessages->where('created_at', '>=' , Carbon::now()->subMinutes($interval));

            $chatUserMessages = clone $messages;

            if($chatUserMessages->count()){
                $chatUserMessages = $chatUserMessages->get()->each(function($message) use(&$chatUsers) {
                                                      $chatUsers[] =  $message->user_id;
                                                      $chatUsers[] =  $message->chatable_id;
                                                    });

                $userIds = array_unique($chatUsers);
                $traders = $this->userRepository->filterUsing(['users' => $userIds], ['trader'])->get();
                $experts = $this->userRepository->filterUsing(['users' => $userIds], ['expert'])->get();

                foreach ($traders as $key => $trader) {

                    foreach ($experts as $key => $expert) {

                        $allMessagesSentToExpert = clone $messages;
                        $allMessagesSentToExpert = $allMessagesSentToExpert->where('user_id', $trader->id)
                                                       ->where('chatable_id', $expert->id);

                        $sentMessagesToExpert = clone $intervalMessages;
                        $lastMessageNotified  = $sentMessagesToExpert->where('user_id', $trader->id)
                                                       ->where('chatable_id', $expert->id)
                                                       ->where('notified', 1)
                                                       ->first();

                        if(!$lastMessageNotified) {
                            $lastMessageNotified = clone $allMessagesSentToExpert;
                            $lastMessageNotified = $lastMessageNotified->where('notified', 1)->first();

                            if(!$lastMessageNotified) {
                                $sentMessagesToExpert = clone $allMessagesSentToExpert;
                                $notifyFor = $sentMessagesToExpert->get()->last();
                                if($notifyFor) {
                                  $this->notify($notifyFor, $trader, $expert);
                                }
                            } else {
                                $lastMessageSentToExpert = clone $allMessagesSentToExpert;
                                $lastMessageSentToExpert = $lastMessageSentToExpert->first();

                                if($lastMessageSentToExpert) {
                                    $lastMessageSentAt       = $lastMessageSentToExpert->created_at->copy();
                                    $lastMessageNotifiedAt   = $lastMessageNotified->created_at;

                                    if($lastMessageSentAt->diffInMinutes($lastMessageNotifiedAt) > $interval) {
                                        $repliesFromExpert  = clone $intervalMessages;
                                        $startTime          = $lastMessageSentToExpert->created_at;
                                        $endTime            = $lastMessageSentToExpert->created_at->copy()->addMinutes($interval);

                                        $repliesFromExpert  = $repliesFromExpert->select('id', 'user_id', 'chatable_id', 'notified', 'created_at')
                                                                    ->where('user_id', $expert->id)
                                                                    ->where('chatable_id', $trader->id)
                                                                    ->whereBetween('created_at', [
                                                                        $startTime, $endTime
                                                                    ]);

                                        if(Carbon::now()->diffInMinutes($lastMessageSentToExpert->created_at) >= $interval) {
                                            if(!$repliesFromExpert->count()) {
                                                $notifyFor = $lastMessageSentToExpert;
                                                if($notifyFor) {
                                                    $this->notify($notifyFor, $trader, $expert);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    private function notify($message, $trader, $expert)
    {
        \Log::info('Notified-for-message-id-'. $message->id);
        $this->chatMessageRepo->update($message, ['notified' => 1]);
        event(new NotifyExpertForMessage($trader, $expert));
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['interval', InputArgument::REQUIRED, 'An interval argument.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
