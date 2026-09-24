<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    customer: Object,
    orders: Object,
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
</script>

<template>
    <AdminLayout :title="`Pedidos de ${customer.name}`">
        <template #header>
            <h1 class="text-lg font-semibold text-stone-900">Pedidos de {{ customer.name }}</h1>
        </template>

        <div class="max-w-3xl">
            <Link
                :href="route('admin.customers.index')"
                class="mb-3 inline-flex items-center gap-1.5 text-sm font-medium text-stone-500 hover:text-primary-700"
            >
                <i class="pi pi-arrow-left text-xs" /> Volver a clientes
            </Link>

            <div class="mb-4 rounded-lg border border-stone-200 bg-white p-4">
                <p class="font-medium text-stone-900">{{ customer.name }}</p>
                <p class="text-sm text-stone-500">{{ customer.email }}</p>
            </div>

            <p v-if="orders.data.length === 0" class="rounded-lg border border-stone-200 bg-white p-10 text-center text-stone-500">
                Este cliente aún no ha hecho pedidos.
            </p>

            <ul v-else class="space-y-3">
                <li v-for="order in orders.data" :key="order.id">
                    <Link
                        :href="route('admin.orders.show', order.id)"
                        class="block rounded-lg border border-stone-200 bg-white p-4 hover:border-primary-400"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-stone-900">{{ order.order_number }}</p>
                                <p class="text-sm text-stone-500">{{ new Date(order.placed_at).toLocaleDateString('es-GT') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-primary-700">Q {{ order.total }}</p>
                                <span class="rounded-full px-2 py-0.5 text-xs" :class="statusStyles[order.status]">
                                    {{ statusLabels[order.status] }}
                                </span>
                            </div>
                        </div>
                    </Link>
                </li>
            </ul>

            <div v-if="orders.data.length > 0" class="mt-4 flex flex-wrap gap-1">
                <Link
                    v-for="(link, index) in orders.links"
                    :key="index"
                    :href="link.url ?? '#'"
                    v-html="link.label"
                    class="rounded px-3 py-1 text-sm"
                    :class="[
                        link.active ? 'bg-primary-700 text-white' : 'text-stone-600 hover:bg-stone-100',
                        !link.url && 'pointer-events-none opacity-50',
                    ]"
                />
            </div>
        </div>
    </AdminLayout>
</template>
