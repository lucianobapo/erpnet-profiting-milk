<?php

namespace ErpNET\Profiting\Milk\Http\Requests;

use App\Http\Requests\Request;
use Date;

class Production extends Request
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
        return [
            'posted_at' => 'required|date_format:Y-m-d',
            'quantity' => 'required|integer',
            'vendor_id' => 'nullable|integer',
            //'category_id' => 'required|integer',
            'attachment' => 'mimes:' . setting('general.file_types') . '|between:0,' . setting('general.file_size') * 1024,
        ];
    }
    
    public function withValidator($validator)
    {
        if ($validator->errors()->count()) {
            $date = Date::parse($this->request->get('posted_at'))->format('Y-m-d');
            
            $this->request->set('posted_at', $date);
        }
    }
}
