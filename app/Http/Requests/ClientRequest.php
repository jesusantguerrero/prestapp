<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientRequest extends FormRequest
{
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'names' => 'required',
            'lastnames' => 'required',
            'dni' => 'alpha_num',
            'dni_type' => 'string',
            'address_details' => 'string',
            'email' => 'string',
            'cellphone' => 'alpha_num'
        ];
    }
}
