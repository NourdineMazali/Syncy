<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class Share extends FormRequest
{
    public $max_date;
    public $min_date;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $this->max_date = Carbon::now()->addMonth()->format('d-m-Y H:i:s');
        $this->min_date = Carbon::now()->format('d-m-Y H:i:s');
        return [
            'page_id' => 'required',
            'scheduled_at'=> 'required|date_format:d-m-Y H:i:s|after:today|before:' . $this->max_date . '|after:' . $this->min_date,
            'caption' => 'required'
        ];
    }


    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {

        return [
            'scheduled_at.required' => 'the scheduled Date time is required',
            'scheduled_at.date_format' => 'the scheduled Date time format is invalid',
            'pins.*.scheduled_at.before' => 'the scheduled date should be before : ' . $this->max_date,
            'pins.*.page_id.required' => 'Please select a Facebook page ',
            'pins.*.to.*.required' => 'Please chose one of the platforms (Facebook or Instagram) to share The pin'

        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function formatErrors(Validator $validator)
    {
        return ['errors' => $validator->errors()->all()];
    }
}
