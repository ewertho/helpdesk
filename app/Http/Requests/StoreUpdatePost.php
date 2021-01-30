<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class StoreUpdatePost extends FormRequest
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
        $id = $this->segment(2);
        $rule = [

            'title'=>[
                'required',
                'min:3',
                'max:160',
                Rule::unique('posts')->ignore($id)
            ],//unique:posts,title,{$id},id

            'content'=>[
                'nullable',
                'min:5',
                'max:10000'
            ],
            
            'image'=>[
                'required',
                'image'
            ],
        ];

        if($this->method() == 'PUT'){
            $rule['image'] = ['nullable', 'image'];
        }

        return $rule;
    }
}
