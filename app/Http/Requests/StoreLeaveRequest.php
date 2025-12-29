<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Services\LeaveBalanceService;
use Carbon\Carbon;

class StoreLeaveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'leave_days' => 'required|integer|min:1',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'leave_type_id.required' => 'Please select a leave type.',
            'leave_type_id.exists' => 'Invalid leave type selected.',
            'start_date.required' => 'Start date is required.',
            'start_date.after_or_equal' => 'Start date must be today or in the future.',
            'end_date.required' => 'End date is required.',
            'end_date.after_or_equal' => 'End date must be on or after start date.',
            'leave_days.required' => 'Number of leave days is required.',
            'leave_days.min' => 'Leave must be at least 1 day.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $staff = auth()->user()->staff;

            if (!$staff) {
                $validator->errors()->add('general', 'Your staff profile is not found.');
                return;
            }

            $leaveService = new LeaveBalanceService();
            $days = $this->input('leave_days');

            // Check leave balance
            if (!$leaveService->canApply($staff->id, $days)) {
                $balance = $leaveService->getBalance($staff->id);
                $validator->errors()->add('leave_days', "Insufficient leave balance. You have {$balance} days remaining.");
            }

            // Check overlapping dates
            if ($leaveService->hasOverlappingLeave($staff->id, $this->input('start_date'), $this->input('end_date'))) {
                $validator->errors()->add('start_date', 'You already have a leave request for these dates.');
            }
        });
    }
}
