<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router } from '@inertiajs/vue3';
import Button from 'primevue/button';

defineProps({
    payments: Object,
});

function verify(payment) {
    router.post(route('admin.payments.verify', payment.id), {}, { preserveScroll: true });
}

function reject(payment) {
    if (confirm('¿Rechazar este pago?')) {
        router.post(route('admin.payments.reject', payment.id), {}, { preserveScroll: true });
    }
}
</script>

<template>
    <AdminLayout title="Pagos por verificar">
        <template #header>
            <h1 class="text-lg font-semibold text-stone-900">Pagos por verificar (transferencias)</h1>
        </template>

        <div class="space-y-4">
            <div
                v-for="payment in payments.data"
                :key="payment.id"
                class="flex items-center justify-between rounded-lg border border-stone-200 bg-white p-4"
            >
                <div>
                    <p class="font-medium text-stone-900">Pedido {{ payment.order.order_number }}</p>
                    <p class="text-sm text-stone-500">{{ payment.order.user?.name }} — Q {{ payment.amount }}</p>
                    <a
                        v-if="payment.proof_path"
                        :href="`/storage/${payment.proof_path}`"
                        target="_blank"
                        class="text-sm text-primary-700 hover:underline"
                    >
                        Ver comprobante
                    </a>
                </div>
                <div class="flex gap-2">
                    <Button label="Verificar" size="small" @click="verify(payment)" />
                    <Button label="Rechazar" size="small" severity="danger" outlined @click="reject(payment)" />
                </div>
            </div>

            <p v-if="payments.data.length === 0" class="rounded-lg border border-stone-200 bg-white p-10 text-center text-stone-500">
                No hay pagos pendientes de verificación.
            </p>
        </div>
    </AdminLayout>
</template>
