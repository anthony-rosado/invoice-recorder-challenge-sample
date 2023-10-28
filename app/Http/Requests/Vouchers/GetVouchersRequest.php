<?php

namespace App\Http\Requests\Vouchers;

use Illuminate\Foundation\Http\FormRequest;

class GetVouchersRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'page' => ['required', 'int', 'gt:0'],
            'paginate' => ['required', 'int', 'gt:0'],
            // rules para los nuevos campos
            'start_date' => ['date_format:Y-m-d\TH:i:sP', 'before_or_equal:end_date'],
            'end_date' => ['date_format:Y-m-d\TH:i:sP', 'after_or_equal:start_date'],
            'series' => ['string'],
            'number' => ['string']
        ];
    }
}
