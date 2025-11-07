<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>{{ config('app.name', 'Laravel') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    @livewireStyles
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/trix/2.0.0/trix.min.css" />

    <style> body { padding:2rem; background:#f8fafb; } </style>
</head>
<body>
    {{ $slot }}

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @livewireScripts
    <script src="https://cdnjs.cloudflare.com/ajax/libs/trix/2.0.0/trix.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        window.addEventListener('swal:confirm', e=>{
            Swal.fire({
                title:e.detail.title,
                text:e.detail.text,
                icon:'warning',
                showCancelButton:true,
                confirmButtonColor:'#3085d6',
                cancelButtonColor:'#d33',
                confirmButtonText:'Ya, hapus!'
            }).then((result)=>{ if(result.isConfirmed) Livewire.emit('deleteConfirmed', e.detail.id) });
        });

        window.addEventListener('swal:success', e=>{
            Swal.fire({ icon:'success', title:e.detail.title, text:e.detail.text, timer:2000, showConfirmButton:false });
        });
    </script>

    @stack('scripts')
</body>
</html>
