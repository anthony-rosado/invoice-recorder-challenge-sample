<?php

namespace App\Jobs;

use App\Events\Vouchers\VouchersCreated;
use App\Models\User;
use App\Services\VoucherService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessStoreVouchersFromXmlContents implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param array $xmlContents
     * @param User $user
     */
    /**
     * Create a new job instance.
     */
    public function __construct(public readonly array $xmlContents, public readonly User $user, private readonly VoucherService $voucherService)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $vouchers = [];
        foreach ($this->xmlContents as $xmlContent) {
            $vouchers[] = $this->voucherService->storeVoucherFromXmlContent($xmlContent, $this->user);
        }

        VouchersCreated::dispatch($vouchers, $this->user);
    }
}
