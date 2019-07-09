<?php

namespace Modules\User\Events\Handlers;

use Illuminate\Support\Facades\App;
use Modules\Notification\Services\AWSSNSPusher;
use Modules\Notification\Services\Pusher;
use Modules\User\Events\ChatMessageWasCreated;
use Modules\Setting\Contracts\Setting;

class SendChatMessageNotification
{
    /**
     * @var Pusher
     */
    private $pusher;

    /**
     * @var AWSSNSPusher
     */
    private $AWSSNSPusher;

    public function __construct(Pusher $pusher, AWSSNSPusher $AWSSNSPusher)
    {
        $this->pusher       = $pusher;
        $this->AWSSNSPusher = $AWSSNSPusher;
    }

    public function handle(ChatMessageWasCreated $event)
    {
        $entity       = $event->entity;

        $sender       = $entity->sender;
        $receiver     = $entity->receiver;

        // build chat message noitification content
        $message = sprintf('%s sent you a message on %s at %s. Read more here.', $sender->present()->fullname(), $entity->created_at->format('d/m/Y'), $entity->created_at->format('g:i A'));

        $title  = 'Message from the ';

        if($sender->hasRoleSlug('expert')) {
          $title .= 'expert';
        } else {
          $title .= 'trader';
        }

        $notification = $this->pusher
             ->forUser($receiver)
             ->push('Message from the expert', $message, 'chat-message-notification-dispatched', $entity, $sender->id);

        $this->AWSSNSPusher
             ->forUser($receiver)
             ->push(app(Setting::class)->get('core::site-name', App::getLocale()), $message, $entity, ['notification_id' => $notification->id]);
    }
}
