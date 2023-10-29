<?php

namespace App\Http\Resources\Vouchers;

use App\Http\Resources\Users\UserResource;
use App\Http\Resources\VoucherLines\VoucherLineResource;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VoucherResource extends JsonResource
{
    /**
     * @var Voucher
     */
    public $resource;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            // se agregaron los nuevos campos a la devolucion 
            'voucher_series'  => $this->resource->voucher_series,
            'voucher_number'  => $this->resource->voucher_number,
            'voucher_type'  => $this->resource->voucher_type,
            'currency'  => $this->resource->currency,
            'issuer_name' => $this->resource->issuer_name,
            'issuer_document_type' => $this->resource->issuer_document_type,
            'issuer_document_number' => $this->resource->issuer_document_number,
            'receiver_name' => $this->resource->receiver_name,
            'receiver_document_type' => $this->resource->receiver_document_type,
            'receiver_document_number' => $this->resource->receiver_document_number,
            'total_amount' => $this->resource->total_amount,
            'user' => $this->whenLoaded(
                'user',
                fn () => UserResource::make($this->resource->user),
            ),
            'lines' => $this->whenLoaded(
                'lines',
                fn () => VoucherLineResource::collection($this->resource->lines),
            ),
        ];
    }
}
