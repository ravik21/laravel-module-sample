<?php

namespace Modules\User\Http\Requests\Admin;

use Modules\Core\Internationalisation\BaseFormRequest;

class UserNudgeRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'message' => 'required|max:140',
            'scheduled_at' => 'sometimes|nullable|after:now',
            'scheduled_at_time' => 'required_with:scheduled_at_date',
            'scheduled_at_date' => 'required_with:scheduled_at_time',
        ];
    }

    public function translationRules()
    {
        return [];
    }

    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [
            'scheduled_at_time.required_with' => 'Please pick the time.',
            'scheduled_at_date.required_with' => 'Please pick the date.',
        ];
    }

    public function translationMessages()
    {
        return [];
    }

    public function prepareForValidation()
    {
        if ($this->get('scheduled_at_time', false) && $this->get('scheduled_at_date', false)) {
            $scheduled_at = sprintf('%s %s:00', $this->get('scheduled_at_date'), $this->get('scheduled_at_time'));
            $this->merge(['scheduled_at' => $scheduled_at]);
        }
    }
}
