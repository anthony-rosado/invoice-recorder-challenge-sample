<?php

namespace App\Http\Requests\Vouchers;

use Illuminate\Foundation\Http\FormRequest;

class StoreVouchersRequest extends FormRequest
{


    public function rules(): array
    {
        return [
            'files' => [
                'required',
                'array',
                'min:1',  // Asegura que al menos un archivo se haya subido
                function ($attribute, $value, $fail) {
                    foreach ($value as $file) {
                        $ext = $file->getClientOriginalExtension();
                        if ($ext !== 'xml') {
                            $fail($attribute . '\'s extension is invalid.');
                        }
                    }
                },
            ],
        ];
    }
}
