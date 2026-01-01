<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\RoleEnum;

class ApproveLeaveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth()->user();
        $staff = $user->staff;

        // Only Admin, HR, CEO, OPS, HOD can approve
        // RoleEnum: ADMIN=1, HR=2, CEO=3, OPS=4, HOD=5
        return $staff && in_array($staff->role_id, [
            RoleEnum::ADMIN->value,
            RoleEnum::HR->value,
            RoleEnum::CEO->value,
            RoleEnum::OPS->value,
            RoleEnum::HOD->value,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'action' => 'required|in:approve,reject,recommend',
            'reason' => 'required_if:action,reject|nullable|string|max:500',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'action.required' => 'Please select an action.',
            'action.in' => 'Invalid action selected.',
            'reason.required_if' => 'Please provide a reason for rejection.',
        ];
    }
}
