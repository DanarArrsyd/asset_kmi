<?php

namespace App\Http\Requests;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStockOpnameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('recordStockOpname', $this->route('asset'));
    }

    public function rules(): array
    {
        return [
            'condition' => ['required', Rule::enum(AssetCondition::class)],
            'status' => ['required', Rule::enum(AssetStatus::class)],
            'notes' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
}
