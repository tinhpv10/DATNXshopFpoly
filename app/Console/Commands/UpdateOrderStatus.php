<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use Carbon\Carbon;

class UpdateOrderStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:update-status';
    protected $description = 'Update order statuses based on age';


    /**
     * The console command description.
     *
     * @var string
     */


    /**
     * Execute the console command.
     */
    public function handle()
    {
//        $twentyMinutesAgo = Carbon::now()->subMinutes(2);
        $twoDaysAgo = Carbon::now()->subDays(2);
        // Cập nhật trạng thái cho các đơn hàng cũ
        Order::where('status', 'Chờ lấy hàng')
            ->whereDate('created_at', '<=', $twoDaysAgo)
            ->chunkById(100, function ($orders) {
                foreach ($orders as $order) {
                    $order->status = 'Chưa xử lý';
                    $order->save();
                }
            });

        $this->info('Order statuses updated successfully.');
    }
}
