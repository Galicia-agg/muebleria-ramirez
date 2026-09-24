<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link } from '@inertiajs/vue3';
import Button from 'primevue/button';

defineProps({
    customers: Object,
});
</script>

<template>
    <AdminLayout title="Clientes">
        <template #header>
            <h1 class="text-lg font-semibold text-stone-900">Clientes</h1>
        </template>

        <template #actions>
            <Link :href="route('admin.customers.create')">
                <Button label="Nuevo cliente" icon="pi pi-plus" />
            </Link>
        </template>

        <p v-if="customers.data.length === 0" class="rounded-lg border border-stone-200 bg-white p-10 text-center text-stone-500">
            Aún no hay clientes registrados.
        </p>

        <template v-else>
            <!-- Mobile: stacked cards -->
            <ul class="space-y-3 sm:hidden">
                <li v-for="customer in customers.data" :key="customer.id" class="rounded-lg border border-stone-200 bg-white p-4">
                    <p class="font-medium text-stone-900">{{ customer.name }}</p>
                    <p class="truncate text-sm text-stone-500">{{ customer.email }}</p>
                    <p class="mt-1 text-xs text-stone-500">
                        {{ customer.orders_count }} pedido{{ customer.orders_count === 1 ? '' : 's' }}
                    </p>
                    <div class="mt-3 border-t border-stone-100 pt-3 text-sm">
                        <Link :href="route('admin.customers.orders', customer.id)" class="font-medium text-stone-600">Pedidos</Link>
                    </div>
                </li>
            </ul>

            <!-- Desktop: table -->
            <div class="hidden overflow-hidden rounded-lg border border-stone-200 bg-white sm:block">
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-stone-200">
                    <thead class="bg-stone-50">
                        <tr class="text-left text-xs font-semibold uppercase tracking-wide text-stone-500">
                            <th class="px-6 py-3">Nombre</th>
                            <th class="px-6 py-3">Email</th>
                            <th class="px-6 py-3">Pedidos</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100 text-sm text-stone-700">
                        <tr v-for="customer in customers.data" :key="customer.id">
                            <td class="px-6 py-3 font-medium text-stone-900">{{ customer.name }}</td>
                            <td class="px-6 py-3">{{ customer.email }}</td>
                            <td class="px-6 py-3">{{ customer.orders_count }}</td>
                            <td class="px-6 py-3 text-right">
                                <Link :href="route('admin.customers.orders', customer.id)" class="font-medium text-stone-600 hover:text-stone-900">Pedidos</Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
              </div>
            </div>

            <div class="mt-4 flex flex-wrap gap-1">
                <Link
                    v-for="(link, index) in customers.links"
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
        </template>
    </AdminLayout>
</template>
