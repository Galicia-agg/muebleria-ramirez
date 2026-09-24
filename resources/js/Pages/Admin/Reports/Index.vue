<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import SalesTrendChart from '@/Components/SalesTrendChart.vue';
import { router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    filters: Object,
    summary: Object,
    salesTrend: Array,
    trendBucket: String,
    topProducts: Array,
});

const presets = [
    { value: 'today', label: 'Hoy' },
    { value: 'yesterday', label: 'Ayer' },
    { value: 'week', label: 'Esta semana' },
    { value: 'month', label: 'Este mes' },
    { value: 'year', label: 'Este año' },
];

const customFrom = ref(props.filters.period === 'custom' ? props.filters.from : props.filters.from);
const customTo = ref(props.filters.period === 'custom' ? props.filters.to : props.filters.to);

function applyPreset(period) {
    router.get(route('admin.reports.index'), { period }, { preserveState: true, replace: true });
}

function applyCustomRange() {
    if (!customFrom.value || !customTo.value) {
        return;
    }
    router.get(
        route('admin.reports.index'),
        { period: 'custom', from: customFrom.value, to: customTo.value },
        { preserveState: true, replace: true },
    );
}

function parseLocalDate(dateStr) {
    const [y, m, d] = dateStr.split('-').map(Number);
    return new Date(y, m - 1, d);
}

const rangeLabel = computed(() => {
    const from = parseLocalDate(props.filters.from).toLocaleDateString('es-GT', { day: 'numeric', month: 'short', year: 'numeric' });
    const to = parseLocalDate(props.filters.to).toLocaleDateString('es-GT', { day: 'numeric', month: 'short', year: 'numeric' });
    return props.filters.from === props.filters.to ? from : `${from} – ${to}`;
});

function formatQ(value) {
    return new Intl.NumberFormat('es-GT', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value ?? 0);
}

function deltaPercent(current, previous) {
    if (!previous) {
        return null;
    }
    return ((current - previous) / previous) * 100;
}

const totalDelta = computed(() => deltaPercent(props.summary.total, props.summary.previousTotal));
const countDelta = computed(() => deltaPercent(props.summary.count, props.summary.previousCount));
</script>

<template>
    <AdminLayout title="Reportes">
        <template #header>
            <h1 class="text-lg font-semibold text-stone-900">Reportes</h1>
        </template>

        <!-- Filters: presets first, custom range after -->
        <div class="mb-6 flex flex-wrap items-center gap-2">
            <button
                v-for="preset in presets"
                :key="preset.value"
                class="rounded-full px-3 py-1.5 text-sm font-medium transition"
                :class="filters.period === preset.value ? 'bg-primary-700 text-white' : 'bg-white text-stone-600 hover:bg-stone-100'"
                @click="applyPreset(preset.value)"
            >
                {{ preset.label }}
            </button>

            <div class="flex w-full flex-wrap items-center gap-1.5 rounded-2xl border border-stone-200 bg-white px-2 py-1.5 sm:ml-1 sm:w-auto sm:rounded-full sm:py-1">
                <input v-model="customFrom" type="date" class="min-w-0 flex-1 rounded border-0 text-sm text-stone-600 focus:ring-0 sm:flex-none" />
                <span class="text-stone-400">–</span>
                <input v-model="customTo" type="date" class="min-w-0 flex-1 rounded border-0 text-sm text-stone-600 focus:ring-0 sm:flex-none" />
                <button
                    class="w-full rounded-full px-2.5 py-1 text-sm font-medium sm:w-auto"
                    :class="filters.period === 'custom' ? 'bg-primary-700 text-white' : 'text-primary-700 hover:bg-primary-50'"
                    @click="applyCustomRange"
                >
                    Aplicar
                </button>
            </div>
        </div>

        <p class="mb-4 text-sm text-stone-500">Período: {{ rangeLabel }}</p>

        <!-- KPI row -->
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3 sm:gap-4">
            <div class="rounded-lg border border-stone-200 bg-white p-4">
                <p class="text-xs font-medium uppercase tracking-wide text-stone-500">Ventas totales</p>
                <p class="mt-1 text-2xl font-semibold text-stone-900">Q {{ formatQ(summary.total) }}</p>
                <p v-if="totalDelta !== null" class="mt-1 text-xs font-medium" :class="totalDelta >= 0 ? 'text-green-600' : 'text-red-600'">
                    <i class="pi" :class="totalDelta >= 0 ? 'pi-arrow-up' : 'pi-arrow-down'" />
                    {{ Math.abs(totalDelta).toFixed(1) }}% vs. período anterior
                </p>
            </div>
            <div class="rounded-lg border border-stone-200 bg-white p-4">
                <p class="text-xs font-medium uppercase tracking-wide text-stone-500">Pedidos</p>
                <p class="mt-1 text-2xl font-semibold text-stone-900">{{ summary.count }}</p>
                <p v-if="countDelta !== null" class="mt-1 text-xs font-medium" :class="countDelta >= 0 ? 'text-green-600' : 'text-red-600'">
                    <i class="pi" :class="countDelta >= 0 ? 'pi-arrow-up' : 'pi-arrow-down'" />
                    {{ Math.abs(countDelta).toFixed(1) }}% vs. período anterior
                </p>
            </div>
            <div class="rounded-lg border border-primary-200 bg-primary-50 p-4">
                <p class="text-xs font-medium uppercase tracking-wide text-primary-700">Ticket promedio</p>
                <p class="mt-1 text-2xl font-semibold text-primary-900">Q {{ formatQ(summary.average) }}</p>
            </div>
        </div>

        <!-- Sales trend -->
        <div class="mt-6 rounded-lg border border-stone-200 bg-white p-5">
            <h2 class="mb-4 font-semibold text-stone-800">Ventas en el período</h2>
            <SalesTrendChart :data="salesTrend" :bucket="trendBucket" />
        </div>

        <!-- Top products -->
        <div class="mt-6 rounded-lg border border-stone-200 bg-white p-5">
            <h2 class="mb-4 font-semibold text-stone-800">Productos más vendidos en el período</h2>
            <p v-if="topProducts.length === 0" class="text-sm text-stone-500">No hay ventas en este período.</p>
            <div v-else class="overflow-x-auto">
                <table class="min-w-full divide-y divide-stone-200 text-sm">
                    <thead>
                        <tr class="text-left text-xs font-semibold uppercase tracking-wide text-stone-500">
                            <th class="py-2 pr-4">#</th>
                            <th class="py-2 pr-4">Producto</th>
                            <th class="py-2 pr-4 text-right">Unidades</th>
                            <th class="py-2 text-right">Ingresos</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        <tr v-for="(product, index) in topProducts" :key="product.product_name">
                            <td class="py-2.5 pr-4 text-stone-500">{{ index + 1 }}</td>
                            <td class="py-2.5 pr-4 font-medium text-stone-900">{{ product.product_name }}</td>
                            <td class="py-2.5 pr-4 text-right text-stone-700">{{ product.quantity_sold }}</td>
                            <td class="py-2.5 text-right font-medium text-stone-900">Q {{ formatQ(product.revenue) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>
