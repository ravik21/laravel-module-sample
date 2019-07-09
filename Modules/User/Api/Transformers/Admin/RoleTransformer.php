<?php namespace Modules\User\Api\Transformers\Admin;

use League\Fractal\TransformerAbstract;
use Cartalyst\Sentinel\Roles\EloquentRole;

class RoleTransformer extends TransformerAbstract
{

    protected $defaultIncludes = [];

    /**
     * Transform User.
     *
     * @param User $user
     * @return League\Fractal\ItemResource
     */
    public function transform(EloquentRole $role)
    {
        return [
            'id' => $role->id,
            'name' => $role->name,
            'slug' => $role->slug,
            'created_at' => $role->created_at->format('H:i:s d/m/Y'),
            'updated_at' => $role->updated_at->format('H:i:s d/m/Y'),
        ];
    }
}
