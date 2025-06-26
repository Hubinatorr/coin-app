<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref } from 'vue';
import type { Header, SortType } from 'vue3-easy-data-table';
import axios from 'axios';
import Vue3EasyDataTable from 'vue3-easy-data-table';
import Echo from 'laravel-echo';

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
const connectionState = ref<'connecting' | 'connected' | 'unavailable'>('connecting');
const channelName = 'currency-updates';

const isLoading = computed(() => connectionState.value !== 'connected' && currencies.value.length === 0);

onMounted(() => {
    axios.get('/api/currencies').then((response) => {
        currencies.value = response.data;
    }).catch((error: Error) => {
        console.log(error);
    })

    echo.connector.pusher.connection.bind('state_change', (states: { previous: string, current: string }) => {
        console.log('Connection state changed from', states.previous, 'to', states.current);
        connectionState.value = states.current;
    });

    echo.channel(channelName)
        .listen('CurrencyDataUpdated', (event: { currencyData: [] }) => {
            console.log('Currency Data Updated');
            currencies.value = event.currencyData;
        });
});

onUnmounted(() => {
    console.log(`Leaving channel: ${channelName}`);
    echo.leave(channelName);
    echo.connector.pusher.connection.unbind_all();
});

const headers: Header[] = [
    { text: 'Name', value: 'name' },
    { text: 'Current Price', value: 'current_price', sortable: true },
    { text: '1h %', value: 'price_change_percentage_1h_in_currency', sortable: true },
    { text: '24h %', value: 'price_change_percentage_24h_in_currency', sortable: true },
    { text: '7d %', value: 'price_change_percentage_7d_in_currency', sortable: true },
    { text: 'Market Cap', value: 'market_cap', sortable: true },
    { text: 'Circulating Supply', value: 'circulating_supply', sortable: true }
];

const sortBy: string[] = [];
const sortType: SortType[] = [];

function formatLargeNumber(value: number): string {
    if (value >= 1e12) return `${(value / 1e12).toFixed(2)}T`;
    if (value >= 1e9) return `${(value / 1e9).toFixed(2)}B`;
    if (value >= 1e6) return `${(value / 1e6).toFixed(2)}M`;
    if (value >= 1e3) return `${(value / 1e3).toFixed(2)}K`;
    return value.toString();
}

function formatPercentChange(value: number): string {
    const arrow = value >= 0 ? '▲' : '▼';
    return `${arrow} ${Math.abs(value).toFixed(2)}%`;
}

function getChangePercentClass(value: number): string {
    return value >= 0 ? 'text-green-400' : 'text-red-400';
}
</script>

<template>
    <div
        class="min-h-screen font-medium p-4 dark-mode">
        <div v-if="connectionState !== 'connected'"
             class="text-center p-4 mb-4 bg-yellow-800 bg-opacity-40 text-yellow-200 rounded-lg">
            <p v-if="connectionState === 'connecting'">Connecting to real-time server...</p>
            <p v-if="connectionState === 'unavailable'">Connection lost. Attempting to reconnect...</p>
        </div>
        <Vue3EasyDataTable
            :headers="headers"
            :items="currencies"
            :sort-by="sortBy"
            :sort-type="sortType"
            :loading="isLoading"
            buttons-pagination
            multi-sort
        >
            <template #item-name="{ name, icon }">
                <div class="flex items-center gap-3">
                    <img v-if="icon" :src="icon" alt="logo" class="w-6 h-6 rounded-full" />
                    <span>{{ name }}</span>
                </div>
            </template>
            <template #item-current_price="{ current_price }">
                ${{ current_price.toLocaleString() }}
            </template>
            <template #item-market_cap="{ market_cap }">
                $ {{ market_cap.toLocaleString() }}
            </template>
            <template #item-price_change_percentage_1h_in_currency="{ price_change_percentage_1h_in_currency: change }">
                <div :class="getChangePercentClass(change)">
                    {{ formatPercentChange(change) }}
                </div>
            </template>
            <template
                #item-price_change_percentage_24h_in_currency="{ price_change_percentage_24h_in_currency: change }">
                <div :class="getChangePercentClass(change)">
                    {{ formatPercentChange(change) }}
                </div>
            </template>
            <template #item-price_change_percentage_7d_in_currency="{ price_change_percentage_7d_in_currency: change }">
                <div :class="getChangePercentClass(change)">
                    {{ formatPercentChange(change) }}
                </div>
            </template>
            <template #item-circulating_supply="{ circulating_supply, symbol }">
                {{ formatLargeNumber(circulating_supply) }} {{ symbol.toUpperCase() }}
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
