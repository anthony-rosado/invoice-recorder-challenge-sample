<?php

namespace App\Jobs;

use App\Events\Vouchers\VouchersCreated;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherLine;
use App\Services\VoucherService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Log\Logger;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use SimpleXMLElement;
use Throwable;

class ProcessStoreVouchersFromXmlContents extends VoucherService implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param string[] $xmlFiles
     * @param User $user
     */
    /**
     * Create a new job instance.
     */
    public function __construct(public readonly array $xmlFiles, public readonly User $user)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // llamar el storeVouchersFromXmlContents para procesar los 
        //comprobantes y enviar la notificacion con los errores
        $this->storeVouchersFromXmlContents($this->xmlFiles, $this->user);
    }
}
