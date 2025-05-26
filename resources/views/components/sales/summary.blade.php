<footer class="mx-auto w-8/12 mt-4 text-sm text-gray-700">
	<p class="mb-2">{{ $date }}</p>
	<div class="flex justify-between border-t pt-2">
		@php
      $words = explode(" ", $title);
    @endphp
		<p>Total {{ str_replace("Daftar ", "", $title) }} : <strong>Rp{{ number_format($summary['total'], 0, ',', '.') }}</strong></p>
		<p>Jumlah Transaksi: <strong>{{ $summary['count'] }}</strong></p>
	</div>
</footer>