<?php

namespace App\Console\Commands;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CancellOrdersByTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cancell-orders-by-time';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancels orders that have not been paid within two minutes.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
		$tiemTwoMinutesAgo = Carbon::now()->subMinutes(2)->format('Y-m-d H:i:s');

        $orders = Order::query()
			->select('id', 'payment_status')
			->where([
				['payment_status', 'for payment'],
				['created_at', '<', $tiemTwoMinutesAgo]
			])
			->get();

		foreach ($orders as $order)
		{
			$order->payment_status = 'cancelled';
			$order->save();
		}
    }
}
