<?php namespace Modules\User\Console;

use Exception;
use Illuminate\Console\Command;
use Modules\Feed\Search\FeedSearch;
use Illuminate\Contracts\Mail\Mailer;
use Modules\User\Emails\User\DigestEmail;
use Modules\User\Events\EmailDigestWasSent;
use Modules\User\Repositories\UserRepository;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Modules\Article\Repositories\ArticleRepository;
use \Carbon\Carbon;

class SendMonthlyDigestEmailCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'user:send-monthly-digest-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send out digest email.';

    /**
     * @var FeedSearch
     */
    protected $search;

    /**
     * @var ArticleRepository
     */
    protected $article;

    /**
     * Create a new command instance.
     *
     * @return void
     */

    public function __construct(UserRepository $user, FeedSearch $search, ArticleRepository $article, Mailer $mail)
    {
        parent::__construct();

        $this->user         = $user;
        $this->search       = $search;
        $this->mail         = $mail;
        $this->article      = $article;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Starting Digest Email Command.');

        $meta   = [];
        $meta['status']    = 'active';
        $meta['order']     = 'desc';
        $meta['order_by']  = 'created_at';

        if($this->option('user')) {
          $userId = $this->option('user');
          $meta['users'] = [$userId];
        }

        if($this->option('roleGroup')) {
          $roleGroup = [$this->option('roleGroup')];
        } else {
          $roleGroup = ['trader'];
        }

        $month = Carbon::now()->subMonth();

        if($this->option('limit')) {
          $limit = $this->option('limit');
        } else {
          $limit = null;
        }

        $users = $this->user->getDigestable($meta, $roleGroup, $limit);
        $this->info(sprintf('Found %s Users', $users->count()));

        foreach ($users as $user) {

            $offsets = [];
            $list = [];

            $this->info('User: ' . $user->present()->fullName() .' | '. $user->email);

            $currentUserTime = Carbon::now()->timezone($user->timezone ? $user->timezone : 'GMT');

            $currentUserTimeOffsets['hour'] = $currentUserTime->format('h');
            $currentUserTimeOffsets['minutes'] = $currentUserTime->format('i');
            $currentUserTimeOffsets['seconds'] = $currentUserTime->format('s');

            if($currentUserTimeOffsets['hour'] != 8 && !$this->option('force')){
              continue;
            }

            $company = $user->companies()->first();

            if (!$company) {
                $this->info('No company for user: ' . $user->present()->fullName());
                continue;
            }

            try {

                $meta = [
                  'user'        => $user,
                  'per_page'    => 6,
                  'type'        => 'Modules\Article\Entities\Article,Modules\Event\Entities\Event',
                  'company_id'  => $company->id,
                  'order_by'    => 'created_at',
                  'order'       => 'desc'
                ];

                if (config('core.search.enabled')) {
                    try {
                        $articles = $this->search->filterAndPaginateUsing($meta);
                    } catch (\Exception $e) {
                        $articles = $this->article->filterAndPaginateUsing($meta);
                    }
                } else {
                    $articles = $this->article->filterAndPaginateUsing($meta);
                }

                $relatedFeedItems = $articles->getCollection();
                $transformedFeedItems = [];
                foreach ($relatedFeedItems as $item) {
                    $feedTransformer      = new \Modules\Feed\Api\Transformers\FeedTransformer($user);
                    $feedFileTransformer  = new \Modules\Article\Api\Transformers\ArticleMediaFileTransformer();

                    $transformed              = $feedTransformer->transform($item);
                    $transformed['mediaFile'] = $item->mediaFile ? $feedFileTransformer->transform($item->mediaFile) : null;
                    $transformedFeedItems[] = $transformed;
                }

                $relatedFeedItems = collect($transformedFeedItems);

                $data = [
                    'user' => $user->toArray(),
                    'feed' => $relatedFeedItems,
                    'month' => $month
                ];

                $this->mail->to($user->email)->send(new DigestEmail($user, $data));
                event($event = new EmailDigestWasSent($user));

            } catch (Exception $e) {
                $this->error('Could not send digest to user - ' . $user->present()->fullName() . ' - due to - ' . $e->getMessage());
            }
        }

        $this->info('Done');

    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
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
          ['user', null, InputOption::VALUE_OPTIONAL, 'A specific user.', null],
          ['roleGroup', null, InputOption::VALUE_OPTIONAL, 'A specific role group.', null],
          ['force', null, InputOption::VALUE_OPTIONAL, 'Forcefully send emails.', null],
          ['limit', null, InputOption::VALUE_OPTIONAL, 'Limit user count.', null],
        ];
    }
}
