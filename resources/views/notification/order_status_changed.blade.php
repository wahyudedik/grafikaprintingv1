@component('mail::message')
    # Order Status Update

    Your order {{ $transaksi->kode }} status has been updated to: {{ $status }}

    Current Progress: {{ $transaksi->progress_percentage }}%

    Estimated completion: {{ $transaksi->estimasi_selesai }}

    Thank you for your business!

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
