<?php namespace Modules\User\Api\Transformers;

use League\Fractal\TransformerAbstract;
use Modules\User\Entities\ChatMessage;

class ChatMessageTransformer extends TransformerAbstract
{

    protected $defaultIncludes = [];

    /**
     * Transform User.
     *
     * @param User $user
     * @return League\Fractal\ItemResource
     */
    public function transform(ChatMessage $chatMessage)
    {
        $payload      = json_decode($chatMessage->payload, true);
        $referenceId  = array_keys($payload)[0];

        $chatable     = $this->chatable($chatMessage);
        $sender       = $chatMessage->sender;

        $transformed[$referenceId] =  [
            'id'              => $chatMessage->id,
            'reference_id'    => $referenceId,
            'body'            => json_decode($chatMessage->body),
            'chatable'        => ['id' => $chatable->id, 'full_name' => $chatable->present()->fullname],
            'sender'          => ['id' => $sender->id, 'full_name' => $sender->present()->fullname],
            'payload'         => $chatMessage->payload,
            'imagePayload'    => isset(reset($payload)['imagePayload']) ? reset($payload)['imagePayload'] : [],
            'updated_at'      => $chatMessage->updated_at->diffForHumans(),
        ];

        return $transformed;
    }

    public function chatable($chatMessage)
    {
        $sender = $chatMessage->sender;
        if($chatMessage->chatable_type == 'Booking') {
          $booking  = $chatMessage->booking;
          if($sender->hasRoleSlug('expert')) {
            $receiver = $booking->trader;
          }
          if($sender->hasRoleSlug('trader')) {
            $receiver = $booking->expert;
          }
        } else {
            $receiver = $chatMessage->receiver;
        }

        return $receiver;
    }
}
