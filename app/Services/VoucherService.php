<?php

namespace App\Services;

use App\Events\Vouchers\VouchersCreated;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherLine;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use SimpleXMLElement;

class VoucherService
{
    public function getVouchers(int $page, int $paginate, $startDate, $endDate, $number, $series): LengthAwarePaginator
    {
        // Obtén el usuario actual
        $user = auth()->user();
        //INFO Undefined method 'vouchers'. 
        // se quita cuando se usa $user = JWTAuth::user();
        // de todas maneras funciona
        $vouchers = $user->vouchers();

        // Se aplican los filtros adicionales
        if ($startDate && $endDate) {
            // Se aanaliza las fechas en formato ISO 8601
            $startDateFormat = Carbon::parse($startDate);
            $endDateFormat = Carbon::parse($endDate);
            $vouchers->whereBetween('created_at', [$startDateFormat, $endDateFormat]);
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
     * @param string[] $xmlFiles
     * @param User $user
     * @return Voucher[]
     */
    public function storeVouchersFromXmlContents(array $xmlFiles, User $user): array
    {
        $vouchers = [];
        $errors = [];

        foreach ($xmlFiles as $xmlFile) {
            try {
                $vouchers[] = $this->storeVoucherFromXmlContent($xmlFile['xmlContents'], $user);
            } catch (Exception $exception) {
                $errors[] = [
                    'errorReason' => 'El comprobante no se ha podido analizar como un XML.',
                    'fileName' => $xmlFile['fileName'],
                    'error' => $exception->getMessage()
                ];
            }
        }

        VouchersCreated::dispatch($vouchers, $user, $errors);

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
        $voucherUniqueIdentifier = (string) $xml->xpath('//cbc:ID')[0];
        $voucherCurrency = (string) $xml->xpath('//cbc:DocumentCurrencyCode')[0];
        $voucherType = (string) $xml->xpath('//cbc:InvoiceTypeCode')[0];

        // Separar la serie y el número
        [$voucherSeries, $voucherNumber] = explode('-', $voucherUniqueIdentifier);

        $voucher = new Voucher([
            'voucher_series' => $voucherSeries,
            'voucher_number' => $voucherNumber,
            'currency' => $voucherCurrency,
            'voucher_type' => $voucherType,
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
