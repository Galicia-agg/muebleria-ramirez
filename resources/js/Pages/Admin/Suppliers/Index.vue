<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import Button from 'primevue/button';

const props = defineProps({
    suppliers: Array,
});

function destroy(supplier) {
    if (confirm(`¿Eliminar al proveedor "${supplier.name}"?`)) {
        router.delete(route('admin.suppliers.destroy', supplier.id));
    }
}
</script>

<template>
    <AdminLayout title="Proveedores">
        <template #header>
            <h1 class="text-lg font-semibold text-stone-900">Proveedores</h1>
        </template>

        <template #actions>
            <Link :href="route('admin.suppliers.create')">
                <Button label="Nuevo proveedor" icon="pi pi-plus" />
            </Link>
        </template>

        <p v-if="suppliers.length === 0" class="rounded-lg border border-stone-200 bg-white p-10 text-center text-stone-500">
            No hay proveedores.
        </p>

        <template v-else>
            <!-- Mobile: stacked cards -->
            <ul class="space-y-3 sm:hidden">
                <li v-for="supplier in suppliers" :key="supplier.id" class="rounded-lg border border-stone-200 bg-white p-4">
                    <div class="flex items-center justify-between">
                        <span class="font-medium text-stone-900">{{ supplier.name }}</span>
                        <span
                            class="rounded-full px-2 py-0.5 text-xs"
                            :class="supplier.active ? 'bg-green-100 text-green-700' : 'bg-stone-100 text-stone-500'"
                        >
                            {{ supplier.active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                    <p class="mt-1 text-sm text-stone-500">{{ supplier.contact_name || '—' }} · {{ supplier.phone || '—' }}</p>
                    <div class="mt-3 flex gap-4 border-t border-stone-100 pt-3 text-sm">
                        <Link :href="route('admin.suppliers.edit', supplier.id)" class="font-medium text-primary-600">Editar</Link>
                        <button class="font-medium text-red-600" @click="destroy(supplier)">Eliminar</button>
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
                            <th class="px-6 py-3">Contacto</th>
                            <th class="px-6 py-3">Teléfono</th>
                            <th class="px-6 py-3">Estado</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100 text-sm text-stone-700">
                        <tr v-for="supplier in suppliers" :key="supplier.id">
                            <td class="px-6 py-3 font-medium text-stone-900">{{ supplier.name }}</td>
                            <td class="px-6 py-3">{{ supplier.contact_name || '—' }}</td>
                            <td class="px-6 py-3">{{ supplier.phone || '—' }}</td>
                            <td class="px-6 py-3">
                                <span
                                    class="rounded-full px-2 py-0.5 text-xs"
                                    :class="supplier.active ? 'bg-green-100 text-green-700' : 'bg-stone-100 text-stone-500'"
                                >
                                    {{ supplier.active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <Link :href="route('admin.suppliers.edit', supplier.id)" class="mr-3 font-medium text-primary-600 hover:text-primary-800">Editar</Link>
                                <button class="font-medium text-red-600 hover:text-red-800" @click="destroy(supplier)">Eliminar</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
              </div>
            </div>
        </template>
    </AdminLayout>
</template>
