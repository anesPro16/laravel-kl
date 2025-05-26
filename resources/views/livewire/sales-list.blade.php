<div>
	<x-sales.header :title="$title" />
	<x-sales.table :sales="$sales" :headers="$headers"  />
	<x-sales.summary :summary="$summary" :date="$date" :$title />
	<x-sales.filter />
	<x-sales.drawer-detail :selectedSale="$selectedSale" :$title isReceipt/>
</div>