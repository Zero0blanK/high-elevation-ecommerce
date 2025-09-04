<?php

namespace App\Mail;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LowStockAlert extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function build()
    {
        return $this->subject('Low Stock Alert: ' . $this->product->name)
            ->view('emails.admin.low_stock_alert')
            ->with([
                'product' => $this->product,
                'adminUrl' => route('admin.products.show', $this->product->id),
                'inventoryUrl' => route('admin.inventory.index')
            ]);
    }
}