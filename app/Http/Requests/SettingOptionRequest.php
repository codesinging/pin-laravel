<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingOptionRequest extends FormRequest
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
            'group_id' => 'required',
            'name' => 'required',
            'key' => 'required',
            'type' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'group_id' => '分组ID',
            'name' => '配置名称',
            'key' => '配置键',
            'type' => '配置输入组件类型',
        ];
    }
}
