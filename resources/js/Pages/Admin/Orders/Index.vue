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

        <div class="overflow-hidden rounded-lg border border-stone-200 bg-white">
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
                        <td class="px-6 py-3">{{ statusLabels[order.status] }}</td>
                        <td class="px-6 py-3 text-right">
                            <Link :href="route('admin.orders.show', order.id)" class="font-medium text-primary-600 hover:text-primary-800">
                                Ver
                            </Link>
                        </td>
                    </tr>
                    <tr v-if="orders.data.length === 0">
                        <td colspan="5" class="px-6 py-10 text-center text-stone-500">No hay pedidos.</td>
                    </tr>
                </tbody>
            </table>
          </div>
        </div>
    </AdminLayout>
</template>
