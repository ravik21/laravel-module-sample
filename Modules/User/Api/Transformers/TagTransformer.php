<?php namespace Modules\User\Api\Transformers;

use Modules\Taggable\Entities\Tag;
use League\Fractal\TransformerAbstract;

class TagTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include.
     *
     * @var array
     */
    protected $availableIncludes = [];

    /**
     * Transform Tag.
     *
     * @param Tag $tag
     * @return League\Fractal\ItemResource
     */
    public function transform(Tag $tag)
    {
        return [
            'id' => $tag->id,
            'name' => $tag->name,
            'slug' => $tag->slug,
            'enabled' => $tag->pivot->enabled
        ];
    }
}
