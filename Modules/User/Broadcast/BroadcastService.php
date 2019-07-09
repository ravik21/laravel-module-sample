<?php
namespace Modules\User\Broadcast;

use Illuminate\Broadcasting\Broadcasters\PusherBroadcaster;
use Pusher\Pusher;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Broadcasting\BroadcastException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class BroadcastService
{
  public $pusher;
  public $pusherBroadCaster;

  public function __construct()
  {
      $this->pusher = new Pusher(
        env('PUSHER_APP_KEY'),
        env('PUSHER_APP_SECRET'),
        env('PUSHER_APP_ID'),[
          'cluster' => env('PUSHER_APP_CLUSTER'),
          'encrypted' => true,
          'useTLS' => true
      ]);
  }

  public function authenticate($request)
  {
    $user['user_id'] = base64_encode($request->id);
    return $this->pusher->socket_auth($request->channel_name, $request->socket_id,json_encode($user,true));
  }


  /**
   * Broadcast the given event.
   *
   * @param  array  $channels
   * @param  string $event
   * @param  array  $payload
   * @return void
   */
    public function broadcast(array $channels, $event, array $payload = [])
    {
        $socket = Arr::pull($payload, 'socket');

        $response = $this->pusher->trigger(
            $this->formatChannels($channels), $event, $payload, $socket, true
        );

        if ((is_array($response) && $response['status'] >= 200 && $response['status'] <= 299)
            || $response === true) {

            return $response;
        }

        throw new BroadcastException(
            is_bool($response) ? 'Failed to connect to Pusher.' : $response['body']
        );
    }

    /**
     * Decode the given Pusher response.
     *
     * @param  mixed  $response
     * @return array
     */
    protected function decodePusherResponse($response)
    {
        return json_decode($response, true);
    }

    /**
     * Format the channel array into an array of strings.
     *
     * @param  array  $channels
     * @return array
     */
    protected function formatChannels(array $channels)
    {
        return array_map(function ($channel) {
            return (string) $channel;
        }, $channels);
    }
}
