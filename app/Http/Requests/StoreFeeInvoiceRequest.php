<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeeInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'student_id' => ['required', 'exists:students,id'],
            'invoice_number' => ['required', 'string', 'max:50', 'unique:fee_invoices,invoice_number'],
            'title' => ['required', 'string', 'max:255'],
            'fee_type' => ['required', 'in:tuition,admission,exam,transport,library,misc'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'due_date' => ['required', 'date'],
            'paid_date' => ['nullable', 'date'],
            'status' => ['required', 'in:paid,unpaid,partial'],
            'payment_method' => ['nullable', 'in:cash,bank_transfer,cheque,online'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
