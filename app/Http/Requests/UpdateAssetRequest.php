<?php

namespace App\Http\Requests;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('asset'));
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'department_id' => ['required', 'exists:departments,id'],
            'location_id' => ['required', 'exists:locations,id'],
            'model' => ['nullable', 'string', 'max:255'],
            'specification' => ['nullable', 'string'],
            'pic' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::enum(AssetStatus::class)],
            'condition' => ['required', Rule::enum(AssetCondition::class)],
            'purchase_date' => ['nullable', 'date'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
}
