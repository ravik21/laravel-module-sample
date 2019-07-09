<?php

namespace Modules\User\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\User\Entities\ChatMessage;
use Modules\User\Entities\Sentinel\User;

use Modules\User\Http\Requests\StoreMessageRequest;
use Modules\User\Http\Requests\GetChatMessageRequest;
use Modules\User\Http\Requests\ChatUsersRequest;
use Modules\User\Http\Requests\ChatUserIsTypingRequest;

use Modules\Core\Http\Controllers\Api\BaseApiController;

use Modules\User\Repositories\ChatMessageRepository;
use Modules\User\Repositories\UserRepository;

use Modules\User\Api\Transformers\ChatMessageTransformer;
use Modules\User\Api\Transformers\UserTransformer;
use Modules\Notification\Repositories\NotificationRepository;

class ChatMessageController extends BaseApiController
{
    /**
      * @var ChatMessageRepository
      */

    /**
      * @var UserRepository
      */

    protected $chatMessage;
    protected $userRepository;

    public function __construct(ChatMessageRepository $chatMessage, UserRepository $userRepository)
    {
        $this->chatMessage    = $chatMessage;
        $this->userRepository = $userRepository;

        parent::__construct();
    }

    /**
     * Display a listing of the users.
     * @return Response
     */
    public function users(ChatUsersRequest $request)
    {
        $meta  = $request->only('search', 'status', 'suspended', 'active_user', 'start_chat_with', 'groups');

        $chatMessages = $this->chatMessage->filterUsing([
          'user_id'  => $meta['active_user']
        ]);

        $chatUsers = [];
        $chatRecievedFromUsers = clone $chatMessages;
        $chatRecievedFromUsers = $chatRecievedFromUsers->receivedFromUsers(['user_id' => $meta['active_user']])
                                                       ->pluck('id','user_id')
                                                       ->toArray();

        $chatRecievedFromUsers = array_flip($chatRecievedFromUsers);

        $chatSentToUsers = clone $chatMessages;
        $chatSentToUsers = $chatSentToUsers->sentToUsers(['user_id' => $meta['active_user']])
                                            ->pluck('id','chatable_id')
                                            ->toArray();

        $chatSentToUsers = array_flip($chatSentToUsers);

        $chatUsers = $chatRecievedFromUsers + $chatSentToUsers;

        ksort($chatUsers);
        $chatUsers = array_flip(array_flip($chatUsers));
        krsort($chatUsers);

        if($request->start_chat_with) {
          $chatUsers = $request->start_chat_with + $chatUsers;
        }

        $chatUsers = array_values(array_unique($chatUsers));

        $chatUsersImploded = implode(',',array_fill(0, count($chatUsers), '?'));

        $users = $this->userRepository->filterUsing($meta, $meta['groups']);
        $users = $users->whereIn('id', $chatUsers);

        if(count($chatUsers)) {
          $users = $users->orderByRaw("field(id,{$chatUsersImploded})", $chatUsers);
        }

        return $this->responder->parseIncludes(request()->get('includes', []))
                              ->collection($users->get(), new UserTransformer())
                              ->get();
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function messages(GetChatMessageRequest $request)
    {
        $meta = $request->only('search','per_page','page','user_id', 'chatable_id', 'chatable_type', 'logged_in', 'start_date', 'end_date', 'order', 'order_by', 'go_to_message', 'is_search');

        $chatMessages = $this->chatMessage->filterAndPaginateUsing($meta);

        return $this->responder
                    ->collection($chatMessages->getCollection(), new ChatMessageTransformer())
                    ->withPaginator($chatMessages)
                    ->get();
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('user::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(StoreMessageRequest $request)
    {
        $payload      = $request->payload;
        $chatMessage   = $this->chatMessage->create($payload);

        return $this->responder
                    ->item($chatMessage, new ChatMessageTransformer())
                    ->get();
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function broadcast(StoreMessageRequest $request)
    {
        $response = $this->chatMessage->broadCastMessage($request);
        return response()->json($response, 200);
    }

    public function userIsTyping(ChatUserIsTypingRequest $request)
    {
        $meta = $request->only('user_id', 'chatable_id');
        $userIsTyping = $this->chatMessage->broadcastIsTyping($meta);
        return response()->json('success', 200);
    }

    public function messagesNotification(Request $request)
    {
        $notification = app(NotificationRepository::class)->find($request->notification_id);

        if($notification) {
          $chatMessage = $notification->notifiable;
          if($chatMessage) {
            return $this->responder
              ->item($chatMessage, new ChatMessageTransformer())
              ->get();
          }
        }

        return response()->json(['error'], 500);
    }
}
