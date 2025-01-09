<?php

namespace App\Jobs;

use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Exception;

class ProcessOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $order;

    /**
     * Create a new job instance.
     *
     * @param \App\Models\Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::beginTransaction();
        $order = Order::with("orderItems.product")->findOrFail(
            $this->order->id
        );

        foreach ($order->orderItems as $item) {
            $product = $item->product;

            // Deduct stock atomically
            $updated = Product::where("id", $product->id)
                ->where("stock", ">=", $item->quantity)
                ->decrement("stock", $item->quantity);

            if (!$updated) {
                DB::rollBack();
                $order->update(["status" => "failed"]);
                throw new \Exception(
                    "Insufficient stock for product: {$product->name}"
                );
            }
        }

        // Mark the order as confirmed
        $order->update(["status" => "confirmed"]);

        DB::commit();

        // Send order confirmation email
        Mail::to($order->customer->email)->send(
            new OrderConfirmationMail($order)
        );
    }
}
