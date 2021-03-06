<?php

namespace App\Http\Requests\Admin\Product;

use Illuminate\Foundation\Http\FormRequest as Request;

class OrderStatusRequest extends Request
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
     * @return array
     */
    public function rules()
    {
        $validationRule = [];
        $validationRule['name'] = 'required|max:255';
        $validationRule['is_default'] = 'max:255';
        

        return $validationRule;
    }
}
