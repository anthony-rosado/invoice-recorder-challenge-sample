<?php

namespace App\Console\Commands;

use App\Models\Voucher;
use Exception;
use Illuminate\Console\Command;
use SimpleXMLElement;

class UpdateVoucherColumns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'voucher:update-columns';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para regularizar los vouchers con los nuevos campos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            //
            $vouchers = Voucher::all();
            foreach ($vouchers as $voucher) {

                // Procesa el XML y extrae los datos para llenar los nuevos campos
                $xml = new SimpleXMLElement($voucher->xml_content);

                // Extreaer información
                $voucher_unique_identifier = (string) $xml->xpath('//cbc:ID')[0];
                $voucher_currency = (string) $xml->xpath('//cbc:DocumentCurrencyCode')[0];
                $voucher_type = (string) $xml->xpath('//cbc:InvoiceTypeCode')[0];

                // Separar la serie y el número
                [$voucher_series, $voucher_number] = explode('-', $voucher_unique_identifier);

                // Actualizar campos
                $voucher->voucher_series = $voucher_series; // F###
                $voucher->voucher_number = $voucher_number;  // NNNN
                $voucher->currency = $voucher_currency;
                $voucher->voucher_type = $voucher_type;

                // Guardar los vouchers
                $voucher->save();

                $this->info('Campos actualizados en los registros de vouchers.');
            }
        } catch (Exception $exception) {
            // Manejar la excepción
            $this->error('Ocurrió un error al procesar los vouchers: ' . $exception->getMessage());
        }
    }
}
