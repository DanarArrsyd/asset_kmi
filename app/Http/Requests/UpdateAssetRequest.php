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
        $asset = $this->route('asset');

        // A department user passes update() because the asset is currently
        // theirs — the check runs against the department it is in now, not the
        // one being submitted. Without this they can post any other department's
        // id and push the asset out of their own scope, losing it in the process,
        // with nothing recorded anywhere.
        $department = ['required', 'exists:departments,id'];

        if (! $this->user()->can('reassignDepartment', $asset)) {
            $department[] = Rule::in([$asset->department_id]);
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'department_id' => $department,
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

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'department_id.in' => 'Hanya Admin yang bisa memindahkan asset ke departemen lain.',
        ];
    }
}
