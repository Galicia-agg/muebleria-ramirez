<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router, useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';

const props = defineProps({
    order: Object,
});

const statuses = ['pendiente', 'confirmado', 'en_preparacion', 'enviado', 'entregado', 'cancelado'];
const statusLabels = {
    pendiente: 'Pendiente',
    confirmado: 'Confirmado',
    en_preparacion: 'En preparación',
    enviado: 'Enviado',
    entregado: 'Entregado',
    cancelado: 'Cancelado',
};

const form = useForm({ status: props.order.status });

function updateStatus() {
    form.patch(route('admin.orders.status', props.order.id));
}
</script>

<template>
    <AdminLayout :title="`Pedido ${order.order_number}`">
        <template #header>
            <h1 class="text-lg font-semibold text-stone-900">Pedido {{ order.order_number }}</h1>
        </template>

        <div class="grid max-w-4xl gap-6 md:grid-cols-3">
            <div class="rounded-lg border border-stone-200 bg-white p-6 md:col-span-2">
                <h2 class="mb-4 font-semibold text-stone-800">Productos</h2>
                <div class="divide-y divide-stone-100 text-sm">
                    <div v-for="item in order.items" :key="item.id" class="flex justify-between py-2">
                        <span>{{ item.product_name }} × {{ item.quantity }}</span>
                        <span class="font-medium">Q {{ item.subtotal }}</span>
                    </div>
                </div>
                <div class="mt-4 flex justify-between border-t border-stone-200 pt-4 text-base font-bold text-stone-900">
                    <span>Total</span>
                    <span>Q {{ order.total }}</span>
                </div>

                <div v-if="order.address" class="mt-6 text-sm">
                    <h3 class="mb-1 font-semibold text-stone-800">Dirección de envío</h3>
                    <p>{{ order.address.recipient_name }} — {{ order.address.phone }}</p>
                    <p>{{ order.address.address_line }}, {{ order.address.municipality }}, {{ order.address.department }}</p>
                </div>

                <div v-if="order.payment" class="mt-6 text-sm">
                    <h3 class="mb-1 font-semibold text-stone-800">Pago</h3>
                    <p>Método: {{ order.payment.method }}</p>
                    <p>Estado: {{ order.payment.status }}</p>
                    <a v-if="order.payment.proof_path" :href="`/storage/${order.payment.proof_path}`" target="_blank" class="text-primary-700 hover:underline">
                        Ver comprobante
                    </a>
                </div>
            </div>

            <div class="rounded-lg border border-stone-200 bg-white p-6">
                <h2 class="mb-4 font-semibold text-stone-800">Cliente</h2>
                <p class="text-sm">{{ order.user?.name }}</p>
                <p class="text-sm text-stone-500">{{ order.user?.email }}</p>

                <h2 class="mb-2 mt-6 font-semibold text-stone-800">Estado del pedido</h2>
                <select v-model="form.status" class="block w-full rounded-md border-stone-300 text-sm shadow-sm">
                    <option v-for="status in statuses" :key="status" :value="status">
                        {{ statusLabels[status] }}
                    </option>
                </select>
                <Button label="Actualizar estado" class="mt-3 w-full" size="small" @click="updateStatus" />
            </div>
        </div>
    </AdminLayout>
</template>
