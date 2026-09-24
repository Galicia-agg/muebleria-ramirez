<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import SalesTrendChart from '@/Components/SalesTrendChart.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    alerts: Object,
    overview: Object,
    summary: Object,
    salesTrend: Array,
    topProducts: Array,
    recentOrders: Array,
});

const statusLabels = {
    pendiente: 'Pendiente',
    confirmado: 'Confirmado',
    en_preparacion: 'En preparación',
    enviado: 'Enviado',
    entregado: 'Entregado',
    cancelado: 'Cancelado',
};

const statusStyles = {
    pendiente: 'bg-amber-100 text-amber-700',
    confirmado: 'bg-blue-100 text-blue-700',
    en_preparacion: 'bg-indigo-100 text-indigo-700',
    enviado: 'bg-purple-100 text-purple-700',
    entregado: 'bg-green-100 text-green-700',
    cancelado: 'bg-red-100 text-red-700',
};

function formatQ(value) {
    return new Intl.NumberFormat('es-GT', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value ?? 0);
}
</script>

<template>
    <AdminLayout title="Dashboard">
        <template #header>
            <h1 class="text-lg font-semibold text-stone-900">Dashboard</h1>
        </template>

        <!-- Action items: things that need attention right now -->
        <h2 class="mb-2 text-sm font-semibold text-stone-500">Requiere atención</h2>
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 sm:gap-4">
            <Link
                :href="route('admin.payments.index')"
                class="rounded-lg border p-4"
                :class="alerts.pendingPayments > 0 ? 'border-amber-300 bg-amber-50' : 'border-stone-200 bg-white'"
            >
                <p class="text-xs font-medium uppercase tracking-wide" :class="alerts.pendingPayments > 0 ? 'text-amber-700' : 'text-stone-500'">
                    Pagos por verificar
                </p>
                <p class="mt-1 text-2xl font-semibold" :class="alerts.pendingPayments > 0 ? 'text-amber-900' : 'text-stone-900'">
                    {{ alerts.pendingPayments }}
                </p>
            </Link>
            <Link
                :href="route('admin.orders.index', { status: 'pendiente' })"
                class="rounded-lg border p-4"
                :class="alerts.pendingOrders > 0 ? 'border-amber-300 bg-amber-50' : 'border-stone-200 bg-white'"
            >
                <p class="text-xs font-medium uppercase tracking-wide" :class="alerts.pendingOrders > 0 ? 'text-amber-700' : 'text-stone-500'">
                    Pedidos pendientes
                </p>
                <p class="mt-1 text-2xl font-semibold" :class="alerts.pendingOrders > 0 ? 'text-amber-900' : 'text-stone-900'">
                    {{ alerts.pendingOrders }}
                </p>
            </Link>
            <Link
                :href="route('admin.products.index', { stock: 'low' })"
                class="rounded-lg border p-4"
                :class="alerts.lowStock > 0 ? 'border-amber-300 bg-amber-50' : 'border-stone-200 bg-white'"
            >
                <p class="text-xs font-medium uppercase tracking-wide" :class="alerts.lowStock > 0 ? 'text-amber-700' : 'text-stone-500'">
                    Stock bajo
                </p>
                <p class="mt-1 text-2xl font-semibold" :class="alerts.lowStock > 0 ? 'text-amber-900' : 'text-stone-900'">
                    {{ alerts.lowStock }}
                </p>
            </Link>
            <Link
                :href="route('admin.products.index', { stock: 'out' })"
                class="rounded-lg border p-4"
                :class="alerts.outOfStock > 0 ? 'border-red-300 bg-red-50' : 'border-stone-200 bg-white'"
            >
                <p class="text-xs font-medium uppercase tracking-wide" :class="alerts.outOfStock > 0 ? 'text-red-700' : 'text-stone-500'">
                    Agotados
                </p>
                <p class="mt-1 text-2xl font-semibold" :class="alerts.outOfStock > 0 ? 'text-red-900' : 'text-stone-900'">
                    {{ alerts.outOfStock }}
                </p>
            </Link>
        </div>

        <!-- Sales KPIs -->
        <div class="mb-2 mt-6 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-stone-500">Ventas</h2>
            <Link :href="route('admin.reports.index')" class="text-sm font-medium text-primary-600 hover:text-primary-800">
                Ver reportes completos
            </Link>
        </div>
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 sm:gap-4">
            <div class="rounded-lg border border-stone-200 bg-white p-4">
                <p class="text-xs font-medium uppercase tracking-wide text-stone-500">Ventas hoy</p>
                <p class="mt-1 text-2xl font-semibold text-stone-900">Q {{ formatQ(summary.today) }}</p>
            </div>
            <div class="rounded-lg border border-stone-200 bg-white p-4">
                <p class="text-xs font-medium uppercase tracking-wide text-stone-500">Esta semana</p>
                <p class="mt-1 text-2xl font-semibold text-stone-900">Q {{ formatQ(summary.week) }}</p>
            </div>
            <div class="rounded-lg border border-stone-200 bg-white p-4">
                <p class="text-xs font-medium uppercase tracking-wide text-stone-500">Este mes</p>
                <p class="mt-1 text-2xl font-semibold text-stone-900">Q {{ formatQ(summary.month) }}</p>
            </div>
            <div class="rounded-lg border border-primary-200 bg-primary-50 p-4">
                <p class="text-xs font-medium uppercase tracking-wide text-primary-700">Total histórico</p>
                <p class="mt-1 text-2xl font-semibold text-primary-900">Q {{ formatQ(summary.allTime) }}</p>
            </div>
        </div>

        <div class="mt-6 rounded-lg border border-stone-200 bg-white p-5">
            <h2 class="mb-4 font-semibold text-stone-800">Ventas de los últimos 30 días</h2>
            <SalesTrendChart :data="salesTrend" />
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-lg border border-stone-200 bg-white p-5">
                <h2 class="mb-4 font-semibold text-stone-800">Productos más vendidos</h2>
                <p v-if="topProducts.length === 0" class="text-sm text-stone-500">Aún no hay ventas registradas.</p>
                <ol v-else class="divide-y divide-stone-100">
                    <li v-for="(product, index) in topProducts" :key="product.product_name" class="flex items-center justify-between py-2.5 text-sm">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-stone-100 text-xs font-semibold text-stone-600">
                                {{ index + 1 }}
                            </span>
                            <span class="truncate text-stone-800">{{ product.product_name }}</span>
                        </div>
                        <div class="shrink-0 text-right">
                            <p class="font-medium text-stone-900">{{ product.quantity_sold }} und.</p>
                            <p class="text-xs text-stone-500">Q {{ formatQ(product.revenue) }}</p>
                        </div>
                    </li>
                </ol>
            </div>

            <div class="rounded-lg border border-stone-200 bg-white p-5">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="font-semibold text-stone-800">Pedidos recientes</h2>
                    <Link :href="route('admin.orders.index')" class="text-sm font-medium text-primary-600 hover:text-primary-800">
                        Ver todos
                    </Link>
                </div>
                <p v-if="recentOrders.length === 0" class="text-sm text-stone-500">Aún no hay pedidos.</p>
                <ul v-else class="divide-y divide-stone-100">
                    <li v-for="order in recentOrders" :key="order.id">
                        <Link :href="route('admin.orders.show', order.id)" class="flex items-center justify-between py-2.5 text-sm hover:text-primary-700">
                            <div class="min-w-0">
                                <p class="font-medium text-stone-900">{{ order.order_number }}</p>
                                <p class="truncate text-xs text-stone-500">{{ order.user?.name }}</p>
                            </div>
                            <div class="shrink-0 text-right">
                                <p class="font-medium text-stone-900">Q {{ formatQ(order.total) }}</p>
                                <span class="rounded-full px-2 py-0.5 text-xs" :class="statusStyles[order.status]">
                                    {{ statusLabels[order.status] }}
                                </span>
                            </div>
                        </Link>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Business overview -->
        <h2 class="mb-2 mt-6 text-sm font-semibold text-stone-500">Panorama general</h2>
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 sm:gap-4">
            <Link :href="route('admin.products.index')" class="rounded-lg border border-stone-200 bg-white p-4 hover:border-primary-300">
                <p class="text-xs font-medium uppercase tracking-wide text-stone-500">Productos activos</p>
                <p class="mt-1 text-2xl font-semibold text-stone-900">{{ overview.activeProducts }}</p>
            </Link>
            <Link :href="route('admin.customers.index')" class="rounded-lg border border-stone-200 bg-white p-4 hover:border-primary-300">
                <p class="text-xs font-medium uppercase tracking-wide text-stone-500">Clientes</p>
                <p class="mt-1 text-2xl font-semibold text-stone-900">{{ overview.customers }}</p>
            </Link>
            <Link :href="route('admin.orders.index')" class="rounded-lg border border-stone-200 bg-white p-4 hover:border-primary-300">
                <p class="text-xs font-medium uppercase tracking-wide text-stone-500">Pedidos totales</p>
                <p class="mt-1 text-2xl font-semibold text-stone-900">{{ overview.totalOrders }}</p>
            </Link>
            <Link :href="route('admin.suppliers.index')" class="rounded-lg border border-stone-200 bg-white p-4 hover:border-primary-300">
                <p class="text-xs font-medium uppercase tracking-wide text-stone-500">Proveedores activos</p>
                <p class="mt-1 text-2xl font-semibold text-stone-900">{{ overview.activeSuppliers }}</p>
            </Link>
        </div>
    </AdminLayout>
</template>
