<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApproveLeaveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth()->user();
        $staff = $user->staff;

        // Only HR, Admin, DG, Director can approve
        return $staff && in_array($staff->role_id, [1, 2, 3, 4]);
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
