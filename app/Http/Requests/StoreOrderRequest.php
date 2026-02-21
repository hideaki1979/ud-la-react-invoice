<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'orderday' => ['required', 'date'],
            'products' => ['required', 'array', 'min:1'],
            'products.*.id' => ['required', 'exists:products,id'],
            'products.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function attributes()
    {
        return [
            'customer_id' => '客先',
            'orderday' => '注文日',
            'products' => '商品数',
            'products.*.id' => '商品名',
            'products.*.quantity' => '注文数',
        ];
    }

    public function messages()
    {
        return [
            'customer_id.required' => ':attributeを入力してください。',
            'customer_id.exists' => ':attributeの顧客が存在しません。',
            'orderday.required' => ':attributeを入力してください。',
            'orderday.date' => ':attributeは日付入力してください。',
            'products.required' => ':attributeを選択してください',
            'products.min' => ':attributeは1つ以上選択してください',
            'products.*.id.exists' => ':attributeが存在しません。',
            'products.*.id.required' => ':attributeがみつかりません',
            'products.*.quantity.required' => ':attributeを入力してください。',
            'products.*.quantity.integer' => ':attributeは整数で入力してください。',
            'products.*.quantity.min' => ':attributeは1つ以上入力してください',
        ];
    }
}
