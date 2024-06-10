<?php

namespace App\Jobs;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class UpdateCartJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $cart;
    protected $newQuantity;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Cart $cart, int $newQuantity)
    {
        $this->cart = $cart;
        $this->newQuantity = $newQuantity;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $product = $this->cart->product;

        // Begin a transaction
        \DB::beginTransaction();

        // Check if the new quantity is greater than the current cart quantity
        $quantityDifference = $this->newQuantity - $this->cart->quantity;

        if ($quantityDifference > 0) {
            // Check if there is enough product quantity available
            if ($product->quantity >= $quantityDifference) {
                $product->quantity -= $quantityDifference;
                $this->cart->quantity = $this->newQuantity;
            } else {
                Log::error('Not enough product quantity available');
                // Rollback and return early if conditions are not met
                \DB::rollback();
                return;
            }
        } else {
            $product->quantity += abs($quantityDifference);
            $this->cart->quantity = $this->newQuantity;
        }

        // Save the changes
        $product->save();
        $this->cart->save();

        // Commit the transaction
        \DB::commit();
    }
}
