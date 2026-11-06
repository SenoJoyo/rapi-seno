<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>
    // Palet tiga warna: hijau papan tulis, kuning kapur, putih kertas.
    tailwind.config = {
        theme: {
            extend: {
                colors: { board: '#1E3B33', chalk: '#F0C94A', paper: '#F4F6F3' },
                fontFamily: { sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'] },
            },
        },
    };
</script>
<style type="text/tailwindcss">
    @layer components {
        .card { @apply bg-white rounded-xl border border-board/10; }
        .label { @apply block text-sm font-semibold mb-1.5; }
        .hint { @apply text-xs text-board/70 mt-1.5; }
        .field { @apply block w-full rounded-lg border border-board/30 bg-white px-3 py-2.5 text-base text-board placeholder:text-board/40 focus:border-board focus:outline-none focus:ring-2 focus:ring-chalk; }
        .btn { @apply inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-board; }
        .btn-primary { @apply bg-board text-white hover:bg-board/90; }
        .btn-accent { @apply bg-chalk text-board hover:bg-chalk/80; }
        .btn-outline { @apply border border-board/30 text-board hover:bg-board/5; }
        .btn-sm { @apply px-3 py-1.5 text-xs; }
        .badge { @apply inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold; }
        .badge-warn { @apply bg-chalk text-board; }
        .badge-soft { @apply bg-chalk/30 text-board; }
        .badge-done { @apply bg-board text-white; }
        .badge-void { @apply border border-board/30 text-board/70; }
    }
</style>
