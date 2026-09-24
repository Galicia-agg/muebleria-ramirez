<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
    orders: Object,
    filters: Object,
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

function filterByStatus(status) {
    router.get(route('admin.orders.index'), { status: status || undefined }, { preserveState: true });
}
</script>

<template>
    <AdminLayout title="Pedidos">
        <template #header>
            <h1 class="text-lg font-semibold text-stone-900">Pedidos</h1>
        </template>

        <div class="mb-4 flex flex-wrap gap-2">
            <button
                class="rounded-full px-3 py-1 text-sm"
                :class="!filters.status ? 'bg-primary-700 text-white' : 'bg-white text-stone-600'"
                @click="filterByStatus(null)"
            >
                Todos
            </button>
            <button
                v-for="(label, status) in statusLabels"
                :key="status"
                class="rounded-full px-3 py-1 text-sm"
                :class="filters.status === status ? 'bg-primary-700 text-white' : 'bg-white text-stone-600'"
                @click="filterByStatus(status)"
            >
                {{ label }}
            </button>
        </div>

        <p v-if="orders.data.length === 0" class="rounded-lg border border-stone-200 bg-white p-10 text-center text-stone-500">
            No hay pedidos.
        </p>

        <template v-else>
            <!-- Mobile: stacked cards -->
            <ul class="space-y-3 sm:hidden">
                <li v-for="order in orders.data" :key="order.id">
                    <Link :href="route('admin.orders.show', order.id)" class="block rounded-lg border border-stone-200 bg-white p-4">
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-stone-900">{{ order.order_number }}</span>
                            <span class="rounded-full px-2 py-0.5 text-xs" :class="statusStyles[order.status]">
                                {{ statusLabels[order.status] }}
                            </span>
                        </div>
                        <div class="mt-1 flex items-center justify-between text-sm text-stone-500">
                            <span>{{ order.user?.name }}</span>
                            <span class="font-semibold text-stone-800">Q {{ order.total }}</span>
                        </div>
                    </Link>
                </li>
            </ul>

            <!-- Desktop: table -->
            <div class="hidden overflow-hidden rounded-lg border border-stone-200 bg-white sm:block">
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-stone-200">
                    <thead class="bg-stone-50">
                        <tr class="text-left text-xs font-semibold uppercase tracking-wide text-stone-500">
                            <th class="px-6 py-3">Pedido</th>
                            <th class="px-6 py-3">Cliente</th>
                            <th class="px-6 py-3">Total</th>
                            <th class="px-6 py-3">Estado</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100 text-sm text-stone-700">
                        <tr v-for="order in orders.data" :key="order.id">
                            <td class="px-6 py-3 font-medium text-stone-900">{{ order.order_number }}</td>
                            <td class="px-6 py-3">{{ order.user?.name }}</td>
                            <td class="px-6 py-3">Q {{ order.total }}</td>
                            <td class="px-6 py-3">
                                <span class="rounded-full px-2 py-0.5 text-xs" :class="statusStyles[order.status]">
                                    {{ statusLabels[order.status] }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <Link :href="route('admin.orders.show', order.id)" class="font-medium text-primary-600 hover:text-primary-800">
                                    Ver
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
              </div>
            </div>
        </template>
    </AdminLayout>
</template>
