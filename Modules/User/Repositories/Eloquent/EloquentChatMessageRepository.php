<?php

namespace Modules\User\Repositories\Eloquent;

use Illuminate\Pagination\LengthAwarePaginator;

use Carbon\Carbon;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\User\Repositories\ChatMessageRepository;
use Modules\User\Repositories\UserRepository;
use Modules\User\Repositories\FileRepository;
use Modules\User\Repositories\ChatFileRepository;

use Modules\User\Events\ChatMessageWasCreated;
use Modules\User\Events\NotifyExpertForMessage;
use Modules\User\Broadcast\BroadcastService;
use Modules\User\Traits\CanUploadChatMessageFiles;

class EloquentChatMessageRepository extends EloquentBaseRepository implements ChatMessageRepository
{
    use CanUploadChatMessageFiles;

    public function create($data)
    {
        $payload      = json_decode($data, true);
        $referenceId  = array_keys($payload)[0];
        $messageData  = $payload[$referenceId];

        $data = [
          'user_id'       =>  $messageData['sender']['id'],
          'chatable_type' =>  $messageData['chatable']['type'],
          'chatable_id'   =>  $messageData['chatable']['id'],
          'body'          =>  json_encode($messageData['body']),
          'payload'       =>  json_encode($payload),
        ];

        $alreadySentMessages = $this->filterUsing([
          'chatable_id' => $data['chatable_id'],
          'user_id'     => $data['user_id']
        ])->count();

        $chatMessage  = parent::create($data);

        if(isset($payload[$referenceId]['imagePayload']) && isset($payload[$referenceId]['imagePayload']['fullpath'])) {
          $file     = $payload[$referenceId]['imagePayload'];

          unset($file['fullpath']);

          $file     = app(FileRepository::class)->create($file);
          $chatFile = app(ChatFileRepository::class)->create([
            'file_id' => $file->id,
            'chat_id' => $chatMessage->id
          ]);

        }

        if(!$chatMessage->receiver->online_status) {
          event(new ChatMessageWasCreated($chatMessage));
        }

        $sender = $chatMessage->sender;
        if($sender->hasRoleSlug('trader') && !$alreadySentMessages) {
          // event(new NotifyExpertForMessage($sender, $chatMessage->receiver));
        }

        return $chatMessage;
    }

    public function broadCastMessage($request)
    {
        $payload      = json_decode($request->payload, true);
        $referenceId  = array_keys($payload)[0];

        $messageData  = $payload[$referenceId];

        $file = $this->uploadBase64File($messageData);

        if(isset($file['path'])) {
          $payload[$referenceId]['imagePayload'] = $file;
        }

        $transformed[$referenceId] =  [
            'payload'         => json_encode($payload),
            'updated_at'      => Carbon::now()->diffForHumans(),
        ];

        $receiver = app(UserRepository::class)->find($messageData['chatable']['id']);

        if($receiver) {
          // $lastApiToken = $receiver->getLastApiToken();
          app(BroadcastService::class)->broadcast(['alegrant-channel'], 'message-for-'.base64_encode($messageData['chatable']['id']), $transformed);
          app(BroadcastService::class)->broadcast(['alegrant-channel'], 'message-by-'.base64_encode($messageData['sender']['id']), $transformed);
        }

        return $payload;
    }

    public function broadcastIsTyping($meta)
    {
        $response = app(BroadcastService::class)->broadcast(['alegrant-channel'], 'user-typing', [
          'isTyping'    => $meta['user_id'],
          'chatable_id' => $meta['chatable_id'] ]);

        return $response;
    }

    public function filterAndPaginateUsing($meta) : LengthAwarePaginator
    {
        $chatMessages = $this->filterUsing($meta);

        return $chatMessages->select('chat_messages.*')
                        ->orderBy('chat_messages.id', 'desc' )
                        ->paginate($this->hasMeta('per_page', $meta) ? $meta['per_page'] : 15)
                        ->appends($_GET);
    }

    public function filterUsing($meta)
    {
        $chatMessages = $this->model->query();

        if ($this->hasMeta('chatable_id', $meta)) {
          $chatMessages->where(function ($query) use ($meta) {

            $query->where(function ($chats) use ($meta) {
                $chats->where('chat_messages.chatable_id', $meta['chatable_id'])
                      ->where('chat_messages.user_id', $meta['user_id']);
            });

            $query->orWhere(function ($chats) use ($meta) {
                $chats->where('chat_messages.chatable_id', $meta['user_id'])
                      ->where('chat_messages.user_id', $meta['chatable_id']);
            });
          });
        } else {
          if($this->hasMeta('user_id', $meta)) {
            $chatMessages->where(function ($query) use ($meta) {
              $query->where('chat_messages.user_id', $meta['user_id'])
              ->orWhere('chat_messages.chatable_id', $meta['user_id']);
            });
          }
        }

        if(!$this->hasMeta('is_search', $meta)) {

          if ($this->hasMeta('search', $meta)) {
            $term = $meta['search'];
            $chatMessages->where(function ($query) use ($term) {
              $query->where('chat_messages.payload', 'LIKE', "%{$term}%");
              // ->orWhere('last_name', 'LIKE', "%{$term}%")
              // ->orWhere('email', 'LIKE', "%{$term}%")
              // ->orWhere('id', $term);
            });
          }

          if ($this->hasMeta('start_date', $meta)) {
            $startDate = $meta['start_date'];
            $startDate = Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d h:m:i');
            $chatMessages->whereDate('created_at', '>=', $startDate);
          }

          if ($this->hasMeta('end_date', $meta)) {
            $endDate = $meta['end_date'];
            $endDate = Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d h:m:i');
            $chatMessages->whereDate('created_at', '<=', $endDate);
          }

        }

        if($this->hasMeta('order_by', $meta) && $this->hasMeta('order', $meta)) {
          $chatMessages->orderBy($meta['order_by'], $meta['order']);
        }

        return $chatMessages;
    }
}
