<?php 

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
	public function process($cart, $summary, $discount, $discount_type, $paid_method)
	{
		DB::transaction(function () use ($cart, $summary, $discount, $discount_type, $paid_method) {
			$sale = \App\Models\Sale::create([
				'user_id'       => auth()->id(),
				'status'        => 'sold',
				'subtotal'      => $summary['subtotal'],
				'discount'      => $discount,
				'discount_type' => $discount_type,
				'paid_methods' => $paid_method,
				'grand_total'   => $summary['grandTotal'],
				'paid_amount'   => $summary['paid_amount'],
				'change'        => $summary['change'],
			]);

			foreach ($cart->items as $item) {
				$product = Product::find($item->product_id);
				$sale->items()->create([
					'product_id'   => $item->product_id,
					// 'product_name' => $product->product_name ?? 'Produk',
					'quantity'     => $item->quantity,
					'price'        => $item->price_at_time,
					'subtotal'     => $item->quantity * $item->price_at_time,
				]);

				$product?->decrement('stock', $item->quantity);
			}

			$cart->items()->delete();
		});
	}
}