<div>
	<x-sales.header :title="$title" />
	<x-sales.table :sales="$sales" :headers="$headers" isReject />
	<x-sales.summary :summary="$summary" :date="$date" :$title />
	<x-sales.filter />
	<x-sales.drawer-detail :selectedSale="$selectedSale" :$title />
</div>