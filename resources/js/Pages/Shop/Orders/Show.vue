<script setup>
import ShopLayout from '@/Layouts/ShopLayout.vue';

const props = defineProps({
    order: Object,
});

const statusLabels = {
    pendiente: 'Pendiente',
    confirmado: 'Confirmado',
    en_preparacion: 'En preparación',
    enviado: 'Enviado',
    entregado: 'Entregado',
    cancelado: 'Cancelado',
};

const paymentLabels = {
    transferencia: 'Transferencia bancaria',
    contra_entrega: 'Pago contra entrega',
    tarjeta_online: 'Tarjeta en línea',
};

const paymentStatusLabels = {
    pendiente: 'Pendiente de verificación',
    verificado: 'Verificado',
    pagado: 'Pagado',
    fallido: 'Fallido',
    reembolsado: 'Reembolsado',
};
</script>

<template>
    <ShopLayout :title="`Pedido ${order.order_number}`">
        <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
            <h1 class="mb-1 text-xl font-bold text-stone-900">Pedido {{ order.order_number }}</h1>
            <p class="mb-6 text-sm text-stone-500">
                Realizado el {{ new Date(order.placed_at).toLocaleString('es-GT') }} — Estado: {{ statusLabels[order.status] }}
            </p>

            <div class="rounded-lg border border-stone-200 bg-white p-6">
                <h2 class="mb-4 font-semibold text-stone-800">Productos</h2>
                <div class="divide-y divide-stone-100">
                    <div v-for="item in order.items" :key="item.id" class="flex justify-between py-2 text-sm">
                        <span>{{ item.product_name }} × {{ item.quantity }}</span>
                        <span class="font-medium">Q {{ item.subtotal }}</span>
                    </div>
                </div>
                <div class="mt-4 flex justify-between border-t border-stone-200 pt-4 text-base font-bold text-stone-900">
                    <span>Total</span>
                    <span>Q {{ order.total }}</span>
                </div>
            </div>

            <div v-if="order.address" class="mt-6 rounded-lg border border-stone-200 bg-white p-6 text-sm">
                <h2 class="mb-2 font-semibold text-stone-800">Dirección de envío</h2>
                <p>{{ order.address.recipient_name }} — {{ order.address.phone }}</p>
                <p>{{ order.address.address_line }}, {{ order.address.municipality }}, {{ order.address.department }}</p>
            </div>

            <div v-if="order.payment" class="mt-6 rounded-lg border border-stone-200 bg-white p-6 text-sm">
                <h2 class="mb-2 font-semibold text-stone-800">Pago</h2>
                <p>Método: {{ paymentLabels[order.payment.method] }}</p>
                <p>Estado: {{ paymentStatusLabels[order.payment.status] }}</p>
            </div>
        </div>
    </ShopLayout>
</template>
