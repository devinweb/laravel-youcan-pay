<?php
namespace Devinweb\LaravelYouCanPay\Console;

use Devinweb\LaravelYouCanPay\Enums\YouCanPayStatus;
use Devinweb\LaravelYouCanPay\Models\Transaction;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CleanPendingTransactionCommand extends Command
{
    /**
    * The name and signature of the console command.
    *
    * @var string
    */
    protected $signature = 'youcanpay:clean-pending-transactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean all pending transactions based on the tolerance value get it from the youcanpay config file.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $tolerance = config('youcanpay.transaction.tolerance');

        $transactions = Transaction::whereStatus(YouCanPayStatus::pending())
                                ->where('created_at', '<=', Carbon::now())
                                ->where('created_at', '>=', Carbon::now()->subSeconds($tolerance ?? 60*60*24))
                                ->get();
        
        foreach ($transactions as $transaction) {
            $transaction->delete();
        }
    }
}
