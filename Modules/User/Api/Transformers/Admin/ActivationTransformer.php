<?php namespace Modules\User\Api\Transformers\Admin;

use League\Fractal\TransformerAbstract;

class ActivationTransformer extends TransformerAbstract
{

    protected $defaultIncludes = [];

    /**
     * Transform Activation.
     *
     * @param Object $activation
     * @return League\Fractal\ItemResource
     */
    public function transform($activation)
    {
        return [
            'id' => $activation->id,
            'completed' => $activation->completed,
            'completed_at' => $activation->completed ? \Carbon\Carbon::parse($activation->completed_at)->format('d/m/Y') : '',
        ];
    }
}
