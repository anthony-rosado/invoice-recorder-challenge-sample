<?php

namespace App\Services;

use App\Events\Vouchers\VouchersCreated;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherLine;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use SimpleXMLElement;

class VoucherService
{
    public function getVouchers(int $page, int $paginate, $start_date, $end_date, $number, $series): LengthAwarePaginator
    {
        // Obtén el usuario actual
        $user = auth()->user();
        //INFO Undefined method 'vouchers'. 
        // se quita cuando se usa $user = JWTAuth::user();
        // de todas maneras funciona
        $vouchers = $user->vouchers();

        // Se aplican los filtros adicionales
        if ($start_date && $end_date) {
            // Se aanaliza las fechas en formato ISO 8601
            $start_date = Carbon::parse($start_date);
            $end_date = Carbon::parse($end_date);
            $vouchers->whereBetween('created_at', [$start_date, $end_date]);
        }
        // voucher_number
        if ($number) {
            $vouchers->where('voucher_number', $number);
        }
        // voucher_series
        if ($series) {
            $vouchers->where('voucher_series', $series);
        }

        return $vouchers->with(['lines', 'user'])->paginate(perPage: $paginate, page: $page);
    }

    /**
     * @param string[] $xmlContents
     * @param User $user
     * @return Voucher[]
     */
    public function storeVouchersFromXmlContents(array $xmlContents, User $user): array
    {
        $vouchers = [];
        foreach ($xmlContents as $xmlContent) {
            $vouchers[] = $this->storeVoucherFromXmlContent($xmlContent, $user);
        }

        VouchersCreated::dispatch($vouchers, $user);

        return $vouchers;
    }

    public function storeVoucherFromXmlContent(string $xmlContent, User $user): Voucher
    {
        $xml = new SimpleXMLElement($xmlContent);

        $issuerName = (string) $xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyName/cbc:Name')[0];
        $issuerDocumentType = (string) $xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyIdentification/cbc:ID/@schemeID')[0];
        $issuerDocumentNumber = (string) $xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyIdentification/cbc:ID')[0];

        $receiverName = (string) $xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyLegalEntity/cbc:RegistrationName')[0];
        $receiverDocumentType = (string) $xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyIdentification/cbc:ID/@schemeID')[0];
        $receiverDocumentNumber = (string) $xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyIdentification/cbc:ID')[0];

        $totalAmount = (string) $xml->xpath('//cac:LegalMonetaryTotal/cbc:TaxInclusiveAmount')[0];

        // Extreaer información extra
        $voucher_unique_identifier = (string) $xml->xpath('//cbc:ID')[0];
        $voucher_currency = (string) $xml->xpath('//cbc:DocumentCurrencyCode')[0];
        $voucher_type = (string) $xml->xpath('//cbc:InvoiceTypeCode')[0];

        // Separar la serie y el número
        [$voucher_series, $voucher_number] = explode('-', $voucher_unique_identifier);

        $voucher = new Voucher([
            'voucher_series' => $voucher_series,
            'voucher_number' => $voucher_number,
            'currency' => $voucher_currency,
            'voucher_type' => $voucher_type,
            'issuer_name' => $issuerName,
            'issuer_document_type' => $issuerDocumentType,
            'issuer_document_number' => $issuerDocumentNumber,
            'receiver_name' => $receiverName,
            'receiver_document_type' => $receiverDocumentType,
            'receiver_document_number' => $receiverDocumentNumber,
            'total_amount' => $totalAmount,
            'xml_content' => $xmlContent,
            'user_id' => $user->id,
        ]);
        $voucher->save();

        foreach ($xml->xpath('//cac:InvoiceLine') as $invoiceLine) {
            $name = (string) $invoiceLine->xpath('cac:Item/cbc:Description')[0];
            $quantity = (float) $invoiceLine->xpath('cbc:InvoicedQuantity')[0];
            $unitPrice = (float) $invoiceLine->xpath('cac:Price/cbc:PriceAmount')[0];

            $voucherLine = new VoucherLine([
                'name' => $name,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'voucher_id' => $voucher->id,
            ]);

            $voucherLine->save();
        }

        return $voucher;
    }
}
