<script setup>
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
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
</script>

<template>
    <ShopLayout title="Mis pedidos">
        <div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
            <h1 class="mb-6 text-xl font-bold text-stone-900">Mis pedidos</h1>

            <div class="space-y-3">
                <Link
                    v-for="order in orders.data"
                    :key="order.id"
                    :href="route('shop.orders.show', order.id)"
                    class="block rounded-lg border border-stone-200 bg-white p-4 hover:border-primary-400"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-stone-800">{{ order.order_number }}</p>
                            <p class="text-sm text-stone-500">{{ new Date(order.placed_at).toLocaleDateString('es-GT') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-primary-700">Q {{ order.total }}</p>
                            <p class="text-sm text-stone-500">{{ statusLabels[order.status] }}</p>
                        </div>
                    </div>
                </Link>

                <p v-if="orders.data.length === 0" class="text-center text-stone-500">
                    Aún no tienes pedidos.
                </p>
            </div>
        </div>
    </ShopLayout>
</template>
