<script setup lang="ts">
import { ref } from 'vue';
import type { Header, Item, SortType } from 'vue3-easy-data-table';
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

// onMounted(() => {
//     echo.channel('chat').listen('CurrencyDataUpdated', (event) => {
//         console.log(event);
//         currencies.value = event.currencyData;
//     });
// });

const currencies = ref(currenciesData);

const headers: Header[] = [
    { text: 'Name', value: 'name' },
    { text: 'Current Price', value: 'current_price', sortable: true },
    { text: 'Market Cap', value: 'market_cap', sortable: true },
    { text: '1h %', value: 'price_change_percentage_1h_in_currency', sortable: true },
    { text: '24h %', value: 'price_change_percentage_24h_in_currency', sortable: true },
    { text: '7d %', value: 'price_change_percentage_7d_in_currency', sortable: true },
    { text: 'Circulating Supply', value: 'circulating_supply', sortable: true }
];

function formatMarketCap(value: number): string {
    if (value >= 1e12) return `$${(value / 1e12).toFixed(2)}T`;
    if (value >= 1e9) return `$${(value / 1e9).toFixed(2)}B`;
    if (value >= 1e6) return `$${(value / 1e6).toFixed(2)}M`;
    if (value >= 1e3) return `$${(value / 1e3).toFixed(2)}K`;
    return `$${value.toFixed(2)}`;
}

const items: Item[] = currencies.value.map((item) => {
    let res = {};
    Object.keys(item).forEach(key => {
        res[key] = item[key];
    });
    return res;
});

function formatChange(value: number): string {
    const arrow = value >= 0 ? '▲' : '▼';
    const fixed = Math.abs(value).toFixed(2);
    return `${arrow} ${fixed}%`;
}

function getChangeClass(value: number): string {
    return value >= 0 ? 'text-green-400' : 'text-red-400 font-medium';
}

const sortBy: string[] = [];
const sortType: SortType[] = [];

</script>

<template>
    <div
        class=" min-h-screen font-medium items-center bg-[#FDFDFC] p-6 text-[#1b1b18] lg:justify-center lg:p-8 dark:bg-[#0a0a0a] dark-mode">
        <Vue3EasyDataTable
            :headers="headers"
            :items="items"
            buttons-pagination
            :sort-by="sortBy"
            :sort-type="sortType"
            multi-sort
        >
            <template #item-name="{ name, icon }">
                <div class="flex items-center gap-2">
                    <img :src="icon" alt="logo" class="w-5 h-5 rounded-full" />
                    <span>{{ name }}</span>
                </div>
            </template>
            <template #item-current_price="{ current_price }">
                <div>
                    $ {{ current_price }}
                </div>
            </template>
            <template #item-market_cap="{ market_cap }">
                <div>
                   $ {{ market_cap.toLocaleString() }}
                </div>
            </template>
            <template #item-price_change_percentage_1h_in_currency="{ price_change_percentage_1h_in_currency }">
                <div :class="getChangeClass(price_change_percentage_1h_in_currency)">
                    {{ formatChange(price_change_percentage_1h_in_currency) }}
                </div>
            </template>
            <template #item-price_change_percentage_24h_in_currency="{ price_change_percentage_24h_in_currency }">
                <div :class="getChangeClass(price_change_percentage_24h_in_currency)">
                    {{ formatChange(price_change_percentage_24h_in_currency) }}
                </div>
            </template>
            <template #item-price_change_percentage_7d_in_currency="{ price_change_percentage_7d_in_currency }">
                <div :class="getChangeClass(price_change_percentage_7d_in_currency)">
                    {{ formatChange(price_change_percentage_7d_in_currency) }}
                </div>
            </template>
            <template #item-circulating_supply="{ circulating_supply }">
                <div>
                    {{ formatMarketCap(circulating_supply) }}
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
    --easy-table-border: 0px;
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
