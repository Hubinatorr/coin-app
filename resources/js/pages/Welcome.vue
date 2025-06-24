<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';;
import { ref } from 'vue';
import type { BodyRowClassNameFunction, Header, Item, SortType } from 'vue3-easy-data-table';
import { currenciesData } from '@/currencies';

import Vue3EasyDataTable from 'vue3-easy-data-table';
// const echo = new Echo({
//     broadcaster: 'reverb',
//     key: import.meta.env.VITE_REVERB_APP_KEY,
//     wsHost: import.meta.env.VITE_REVERB_HOST,
//     wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
//     wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
//     enabledTransports: ['ws', 'wss'],
//     forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https'
// });

const currencies = ref(currenciesData);



// onMounted(() => {
//     echo.channel('chat').listen('CurrencyDataUpdated', (event) => {
//         console.log(event);
//         currencies.value = event.currencyData;
//     });
// });
const headers: Header[] = [
    {text: 'Name', value: 'name'},
    {text: 'Current Price', value: 'current_price' },
    {text: 'Market Cap', value: 'market_cap' },
    {text: '1h %', value: 'price_change_percentage_1h_in_currency' },
    {text: '24h %', value: 'price_change_percentage_24h_in_currency' },
    {text: '7d %', value: 'price_change_percentage_7d_in_currency' },
    {text: 'Circulating Supply', value: 'circulating_supply' },
]

const items: Item[]= currencies.value.map((item) => {
    let res = {}
    Object.keys(item).forEach(key => {
        res[key] = item[key];
    })
    return res;
})


const sortBy: string[] = [
    "name",
    "current_price",
    "market_cap",
    "price_change_percentage_1h_in_currency",
    "price_change_percentage_7d_in_currency",
    "price_change_percentage_7d_in_currency",
    "circulating_supply"
];
const sortType: SortType[] = ["desc", "asc"];

const bodyRowClassNameFunction: BodyRowClassNameFunction = (): string => {
    return 'p-2';
};

</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div
        class="flex min-h-screen flex-col items-center bg-[#FDFDFC] p-6 text-[#1b1b18] lg:justify-center lg:p-8 dark:bg-[#0a0a0a] dark-mode">
        <Vue3EasyDataTable
            :headers="headers"
            :items="items"
            buttons-pagination
            :body-row-class-name="bodyRowClassNameFunction"
        >
            <template #item-name="{ name, icon }">
                <div class="flex items-center gap-2">
                    <img :src="icon" alt="logo" class="w-5 h-5 rounded-full" />
                    <span>{{ name }}</span>
                </div>
            </template>
        </Vue3EasyDataTable>
    </div>
</template>

<style>
.dark-mode {
    --easy-table-header-background-color: #1e2029;
    --easy-table-body-row-background-color: #0e111b;
    --easy-table-body-row-font-color: #ffffff;
    --easy-table-body-row-font-size: 14px;
    --easy-table-footer-background-color: #1e2029;
    --easy-table-footer-font-color: #c0c7d2;
    --easy-table-border: 1px solid #445269;
    --easy-table-row-border: 1px solid #445269;

    --easy-table-header-font-size: 14px;
    --easy-table-header-height: 50px;
    --easy-table-header-font-color: #c1cad4;

    --easy-table-header-item-padding: 10px 15px;

    --easy-table-body-even-row-font-color: #fff;
    --easy-table-body-even-row-background-color: #4c5d7a;


    --easy-table-body-row-height: 50px;

    --easy-table-body-row-hover-font-color: #2d3a4f;
    --easy-table-body-row-hover-background-color: #eee;

    --easy-table-body-item-padding: 10px 15px;


    --easy-table-footer-font-size: 14px;
    --easy-table-footer-padding: 0px 10px;
    --easy-table-footer-height: 50px;

    --easy-table-rows-per-page-selector-width: 70px;
    --easy-table-rows-per-page-selector-option-padding: 10px;
    --easy-table-rows-per-page-selector-z-index: 1;


    --easy-table-scrollbar-track-color: #2d3a4f;
    --easy-table-scrollbar-color: #2d3a4f;
    --easy-table-scrollbar-thumb-color: #4c5d7a;
    --easy-table-scrollbar-corner-color: #2d3a4f;

    --easy-table-loading-mask-background-color: #2d3a4f;
    background-color: #0e111b;
}
</style>
