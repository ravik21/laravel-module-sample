<?php

namespace Modules\User\Events\Handlers;

use Maatwebsite\Sidebar\Group;
use Maatwebsite\Sidebar\Item;
use Maatwebsite\Sidebar\Menu;
use Modules\Core\Sidebar\AbstractAdminSidebar;

class RegisterUserSidebar extends AbstractAdminSidebar
{
    /**
     * Method used to define your sidebar menu groups and items
     * @param Menu $menu
     * @return Menu
     */
    public function extendWith(Menu $menu)
    {
        $menu->group(trans('core::sidebar.content'), function (Group $group) {
            $group->item(trans('user::users.title.users'), function (Item $item) {
                $item->weight(1);
                $item->icon('person');
                $item->authorize(
                    $this->auth->hasAccess('user.all-users.index') or $this->auth->hasAccess('user.admin-users.index')
                    or $this->auth->hasAccess('user.users.index') or $this->auth->hasAccess('user.roles.index')
                    or $this->auth->hasAccess('user.experts.index') or $this->auth->hasAccess('user.traders.index')
                );

                $item->item(trans('user::users.title.all users'), function (Item $item) {
                    $item->weight(0);
                    $item->icon('person');
                    $item->route('admin.user.all-users.index');
                    $item->authorize(
                        $this->auth->hasAccess('user.all-users.index')
                    );
                });

                $item->item(trans('user::users.title.admin users'), function (Item $item) {
                    $item->weight(1);
                    $item->icon('person');
                    $item->route('admin.user.admin-users.index');
                    $item->authorize(
                        $this->auth->hasAccess('user.admin-users.index')
                    );
                });

                $item->item(trans('user::users.title.experts'), function (Item $item) {
                    $item->weight(2);
                    $item->icon('person');
                    $item->route('admin.user.experts.index');
                    $item->authorize(
                        $this->auth->hasAccess('user.experts.index')
                    );
                });

                $item->item(trans('user::users.title.traders'), function (Item $item) {
                    $item->weight(2);
                    $item->icon('person');
                    $item->route('admin.user.traders.index');
                    $item->authorize(
                        $this->auth->hasAccess('user.traders.index')
                    );
                });

                $item->item(trans('user::roles.title.roles'), function (Item $item) {
                    $item->weight(3);
                    $item->icon('supervised_user_circle');
                    $item->route('admin.user.roles.index');
                    $item->authorize(
                        $this->auth->hasAccess('user.roles.index')
                    );
                });
            });
        });
        $menu->group(trans('user::users.my account'), function (Group $group) {
            $group->weight(110);
            $group->item(trans('user::users.profile'), function (Item $item) {
                $item->weight(0);
                $item->icon('account_circle');
                $item->route('admin.account.profile.edit');
                $item->authorize(
                    $this->auth->hasAccess('user.account.profile.show')
                );
            });
        });

        return $menu;
    }
}
