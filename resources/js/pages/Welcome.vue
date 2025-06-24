<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import Echo from 'laravel-echo';
import { onMounted, ref } from 'vue';



const echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    enabledTransports: ['ws', 'wss'],
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https'
});

const currencies = ref([]);

onMounted(() => {
    echo.channel('chat').listen('CurrencyDataUpdated', (event) => {
        console.log(event);
        currencies.value = event.currencyData;
    });
});
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="flex min-h-screen flex-col items-center bg-[#FDFDFC] p-6 text-[#1b1b18] lg:justify-center lg:p-8 dark:bg-[#0a0a0a]">
        ddd
    </div>
</template>
