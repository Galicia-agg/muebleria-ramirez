<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputError from '@/Components/InputError.vue';
import { useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Image from 'primevue/image';
import { computed } from 'vue';

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

const statusStyles = {
    pendiente: 'bg-amber-100 text-amber-700',
    confirmado: 'bg-blue-100 text-blue-700',
    en_preparacion: 'bg-indigo-100 text-indigo-700',
    enviado: 'bg-purple-100 text-purple-700',
    entregado: 'bg-green-100 text-green-700',
    cancelado: 'bg-red-100 text-red-700',
};

const isFinalStatus = computed(() => ['entregado', 'cancelado'].includes(props.order.status));

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
                <div class="divide-y divide-stone-100">
                    <div v-for="item in order.items" :key="item.id" class="flex items-center gap-3 py-3">
                        <Image
                            v-if="item.product?.images?.[0]"
                            :src="item.product.images[0].url"
                            :alt="item.product_name"
                            preview
                            class="block h-14 w-14 shrink-0"
                            imageClass="h-14 w-14 rounded border border-stone-200 object-cover"
                        />
                        <div v-else class="flex h-14 w-14 shrink-0 items-center justify-center rounded border border-stone-200 bg-stone-100 text-stone-400">
                            <i class="pi pi-image" />
                        </div>

                        <div class="min-w-0 flex-1 text-sm">
                            <p class="font-medium text-stone-900">{{ item.product_name }}</p>
                            <p class="text-stone-500">{{ item.quantity }} × Q {{ item.unit_price }}</p>
                        </div>

                        <span class="shrink-0 font-medium text-stone-900">Q {{ item.subtotal }}</span>
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
                <p class="text-sm break-words">{{ order.user?.name }}</p>
                <p class="text-sm text-stone-500 break-words">{{ order.user?.email }}</p>

                <h2 class="mb-2 mt-6 font-semibold text-stone-800">Estado del pedido</h2>

                <template v-if="isFinalStatus">
                    <span class="inline-block rounded-full px-3 py-1 text-sm" :class="statusStyles[order.status]">
                        {{ statusLabels[order.status] }}
                    </span>
                    <p class="mt-2 text-xs text-stone-500">
                        Este pedido ya está {{ order.status === 'entregado' ? 'entregado' : 'cancelado' }} y no se puede modificar.
                    </p>
                </template>
                <template v-else>
                    <select v-model="form.status" class="block w-full rounded-md border-stone-300 text-sm shadow-sm">
                        <option v-for="status in statuses" :key="status" :value="status">
                            {{ statusLabels[status] }}
                        </option>
                    </select>
                    <InputError :message="form.errors.status" class="mt-2" />
                    <Button label="Actualizar estado" class="mt-3 w-full" size="small" :loading="form.processing" @click="updateStatus" />
                </template>
            </div>
        </div>
    </AdminLayout>
</template>
