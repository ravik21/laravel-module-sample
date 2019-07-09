<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class SentinelGroupSeedTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $this->seedRoles();
        $this->seedPermissions();
    }

    private function seedRoles()
    {
        $groups = Sentinel::getRoleRepository();
        Sentinel::getRoleRepository()->createModel()->truncate();

        $roles = [
            ['name' => 'Super Admin', 'slug' => 'super-admin'],
            ['name' => 'Admin', 'slug' => 'admin'],
            ['name' => 'Expert', 'slug' => 'expert'],
            ['name' => 'Trader', 'slug' => 'trader']
        ];

        foreach ($roles as $role) {
            if (!Sentinel::findRoleBySlug($role['slug'])) {
                $groups->createModel()->create($role);
            }
        }
    }

    private function seedPermissions()
    {
        $this->seedSuperAdminPermissions();
        $this->seedAdminPermissions();
        $this->seedExpertPermissions();
        $this->seedTraderPermissions();
    }

    private function seedSuperAdminPermissions()
    {
        $role = Sentinel::findRoleBySlug('super-admin');

        $role->permissions = [
            /* Article */
            'article.articles.index'                => true,
            'article.articles.show'                 => true,
            'article.articles.create'               => true,
            'article.articles.edit'                 => true,
            'article.articles.destroy'              => true,
            'article.articles.publish'              => true,

            /* Booking */
            'booking.bookings.index'                => true,
            'booking.bookings.show'                 => true,
            'booking.bookings.create'               => true,
            'booking.bookings.edit'                 => true,
            'booking.bookings.destroy'              => true,
            'booking.bookings.invoice'              => true,

            /* Commentable */
            'commentable.commentables.index'        => true,
            'commentable.commentables.store'        => true,
            'commentable.commentables.destroy'      => true,

            /* Company */
            'company.companies.index'               => true,
            'company.locations.index'               => true,
            'company.teams.index'                   => true,

            /* Core */
            'core.sidebar.group'                    => true,

            /* Dashboard */
            'dashboard.index'                       => true,

            /* Event */
            'event.events.index'                    => true,
            'event.events.show'                     => true,
            'event.events.create'                   => true,
            'event.events.edit'                     => true,
            'event.events.destroy'                  => true,
            'event.events.publish'                  => true,
            'event.events.statuses'                 => true,

            /* Feed */
            'feed.feeds.index'                      => true,

            /* Media */
            'media.media.index'                     => true,
            'media.media.create'                    => true,
            'media.media.edit'                      => true,
            'media.media.destroy'                   => true,
            'media.folders.index'                   => true,
            'media.folders.create'                  => true,
            'media.folders.edit'                    => true,
            'media.folders.destroy'                 => true,

            /* Notification */
            'notification.notifications.index'      => true,
            'notification.notifications.read'       => true,
            'notification.notifications.destroy'    => true,
            'notification.notifications.read-all'   => true,
            'notification.notifications.delete-all' => true,

            /* Taggable */
            'taggable.tags.by-namespace'            => true,
            'taggable.tags.by-entities'             => true,

            /* (Super) Admin User */
            'user.admin-users.index'                => true,
            'user.admin-users.create'               => true,
            'user.admin-users.edit'                 => true,
            'user.admin-users.show'                 => true,
            'user.admin-users.roles'                => true,

            /* Trader */
            'user.traders.index'                    => true,
            'user.traders.show'                     => true,
            'user.traders.accept'                   => true,
            'user.traders.reject'                   => true,
            'user.traders.destroy'                  => true,

            /* Expert */
            'user.experts.index'                    => true,
            'user.experts.show'                     => true,
            'user.experts.invite'                   => true,
            'user.experts.accept'                   => true,
            'user.experts.reject'                   => true,
            'user.experts.store'                    => true,
            'user.experts.edit'                     => true,
            'user.experts.destroy'                  => true,

            /* Invoices */
            'user.invoices.index'                   => true,

            'user.profile.edit'                     => true,
            'user.profile.show'                     => true,

            /* All Users */
            'user.users.activate'                   => true,
            'user.users.destroy'                    => true,
            'user.users.suspend'                    => true,
            'user.users.unsuspend'                  => true,
            'user.users.logout'                     => true,

            /* Any Admin Account */
            'user.account.profile.show'             => true,
            'user.account.profile.edit'             => true,

            'group.groups.index'                    => false,
            'group.groups.tags'                     => true,
            'taggable.tags.index'                   => false,

            'taggable.tags.user.index'              => true,
            'group.groups.user.index'               => true

        ];
        $role->save();
    }

    private function seedAdminPermissions()
    {
        $role = Sentinel::findRoleBySlug('admin');

        $role->permissions = [
            /* Article */
            'article.articles.index'                => true,
            'article.articles.show'                 => true,
            'article.articles.create'               => true,
            'article.articles.edit'                 => true,
            'article.articles.destroy'              => true,
            'article.articles.publish'              => true,

            /* Booking */
            'booking.bookings.index'                => true,
            'booking.bookings.show'                 => true,
            'booking.bookings.create'               => true,
            'booking.bookings.edit'                 => true,
            'booking.bookings.destroy'              => true,
            'booking.bookings.invoice'              => true,

            /* Campaign */
            'campaign.campaigns.index'              => false,

            /* Commentable */
            'commentable.commentables.index'        => true,
            'commentable.commentables.store'        => true,
            'commentable.commentables.destroy'      => true,

            /* Core */
            'core.sidebar.group'                    => true,

            /* Company */
            'company.companies.index'               => true,
            'company.locations.index'               => true,
            'company.teams.index'                   => true,

            /* Dashboard */
            'dashboard.index'                       => true,

            /* Event */
            'event.events.index'                    => true,
            'event.events.show'                     => true,
            'event.events.create'                   => true,
            'event.events.edit'                     => true,
            'event.events.destroy'                  => true,
            'event.events.publish'                  => true,
            'event.events.statuses'                 => true,

            /* Feed */
            'feed.feeds.index'                      => true,

            /* Media */
            'media.media.index'                     => true,
            'media.media.create'                    => true,
            'media.media.edit'                      => true,
            'media.media.destroy'                   => true,
            'media.folders.index'                   => true,
            'media.folders.create'                  => true,
            'media.folders.edit'                    => true,
            'media.folders.destroy'                 => true,

            /* Notification */
            'notification.notifications.index'      => true,
            'notification.notifications.read'       => true,
            'notification.notifications.destroy'    => true,
            'notification.notifications.read-all'   => true,
            'notification.notifications.delete-all' => true,

            /* Taggable */
            'taggable.tags.by-namespace'            => true,
            'taggable.tags.by-entities'             => true,

            /* Trader */
            'user.traders.index'                    => true,
            'user.traders.show'                     => true,
            'user.traders.accept'                   => true,
            'user.traders.reject'                   => true,
            'user.traders.destroy'                  => false,

            /* Expert */
            'user.experts.index'                    => true,
            'user.experts.show'                     => true,
            'user.experts.invite'                   => true,
            'user.experts.accept'                   => true,
            'user.experts.reject'                   => true,
            'user.experts.store'                    => true,
            'user.experts.edit'                     => false,
            'user.experts.destroy'                  => false,

            'user.profile.edit'                     => true,
            'user.profile.show'                     => true,

            /* Invoices */
            'user.invoices.index'                   => true,

            /* All Users */
            'user.users.activate'                   => true,
            'user.users.destroy'                    => false,
            'user.users.suspend'                    => true,
            'user.users.unsuspend'                  => true,
            'user.users.logout'                     => true,

            /* Any Admin Account */
            'user.account.profile.show'             => true,
            'user.account.profile.edit'             => true,

            'group.groups.index'                    => false,
            'group.groups.tags'                     => true,
            'taggable.tags.index'                   => false,

            'taggable.tags.user.index'              => true,
            'group.groups.user.index'               => true
        ];
        $role->save();
    }

    public function seedExpertPermissions()
    {
        $role = Sentinel::findRoleBySlug('expert');

        $role->permissions = [
            /* Article */
            'article.articles.index'                => true,
            'article.articles.show'                 => true,
            'article.articles.create'                => true,

            /* Booking */
            'booking.bookings.index'                => true,
            'booking.bookings.cancel'               => true,
            'booking.bookings.decline'              => true,
            'booking.bookings.confirm'              => true,
            'booking.bookings.validate-call'        => true,

            'user.experts.show'                     => true,
            'user.traders.show'                     => true,

            /* Campaign */
            'campaign.campaigns.index'              => true,

            /* Commentable */
            'commentable.commentables.index'        => true,
            'commentable.commentables.store'        => true,
            'commentable.commentables.destroy'      => true,

            /* Company */
            'company.locations.index'               => true,
            'company.teams.index'                   => true,

            /* Event */
            'event.events.index'                    => true,
            'event.events.show'                     => true,
            'event.events.create'                   => true,
            'event.events.edit'                     => true,
            'event.events.destroy'                  => true,
            'event.events.publish'                  => true,
            'event.events.statuses'                 => true,

            /* Feed */
            'feed.feeds.index'                      => true,

            /* Likeable */
            'likeable.likeables.like'               => true,
            'likeable.likeables.unlike'             => true,

            /* Notification */
            'notification.notifications.index'      => true,
            'notification.notifications.read'       => true,
            'notification.notifications.destroy'    => true,
            'notification.notifications.read-all'   => true,
            'notification.notifications.delete-all' => true,

            /* Remindable */
            'remindable.remindables.options'        => true,
            'remindable.remindables.set'            => true,
            'remindable.remindables.unset'          => true,

            /* Taggable */
            'taggable.tags.by-namespace'            => true,
            'taggable.tags.by-entities'             => true,

            /* User */
            'user.profile.edit'                     => true,
            'user.profile.show'                     => true,
            'user.profile.upload-terms-conditions'  => true,
            'user.profile.delete-terms-conditions'  => true,

            'user.users.logout'                     => true,
            'user.users.invite'                     => true,
            'user.users.nudge'                      => false,

            'user.auth.pusher'                      => true,
            'user.auth.status'                      => true,

            /* Invoices */
            'user.invoices.index'                   => true,

            'user.chat.users'                       => true,
            'user.chat.messages'                    => true,
            'user.chat.broadcast'                   => true,
            'user.chat.store'                       => true,

            'media.media.create'                    => true,
            'media.media.destroy'                   => true,
            'media.folders.create'                  => true,
            'media.folders.destroy'                 => true
        ];

        $role->save();
    }

    public function seedTraderPermissions()
    {
        $role = Sentinel::findRoleBySlug('trader');

        $role->permissions = [

            /* User */
            'user.expert.mark-as-favourite'         => true,
            'user.expert.unmark-as-favourite'       => true,
            'user.experts.show'                     => true,

             /* Article */
            'article.articles.index'                => true,
            'article.articles.show'                 => true,

            /*Rateable*/
            'rating.ratings.store'                  => true,

            /* Booking */
            'booking.bookings.index'                => true,
            'booking.bookings.create'               => true,
            'booking.bookings.cancel'               => true,
            'booking.bookings.invoice'              => true,
            'booking.bookings.validate-call'        => true,
            'booking.bookings.decline'              => true,

            /* Campaign */
            'campaign.campaigns.index'              => true,

            /* Commentable */
            'commentable.commentables.index'        => true,
            'commentable.commentables.store'        => true,
            'commentable.commentables.destroy'      => true,

            /* Company */
            'company.locations.index'               => true,
            'company.teams.index'                   => true,

            /* Event */
            'event.events.index'                    => true,
            'event.events.show'                     => true,
            'event.events.create'                   => true,
            'event.events.edit'                     => true,
            'event.events.destroy'                  => true,
            'event.events.publish'                  => true,
            'event.events.statuses'                 => true,

            /* Feed */
            'feed.feeds.index'                      => true,

            /* Likeable */
            'likeable.likeables.like'               => true,
            'likeable.likeables.unlike'             => true,

            /* Notification */
            'notification.notifications.index'      => true,
            'notification.notifications.read'       => true,
            'notification.notifications.destroy'    => true,
            'notification.notifications.read-all'   => true,
            'notification.notifications.delete-all' => true,

            /* Remindable */
            'remindable.remindables.options'        => true,
            'remindable.remindables.set'            => true,
            'remindable.remindables.unset'          => true,

            /* Taggable */
            'taggable.tags.by-namespace'            => true,
            'taggable.tags.by-entities'             => true,

            /* User */
            'user.profile.edit'                     => true,
            'user.profile.show'                     => true,
            'user.users.logout'                     => true,
            'user.users.invite'                     => true,
            'user.users.nudge'                      => false,

            'user.auth.pusher'                      => true,
            'user.chat.users'                       => true,
            'user.auth.status'                      => true,
            'user.chat.broadcast'                   => true,
            'user.chat.messages'                    => true,
            'user.chat.store'                       => true,

            /* Trader */
            'user.traders.invite'                   => true
        ];

        $role->save();
    }
}
