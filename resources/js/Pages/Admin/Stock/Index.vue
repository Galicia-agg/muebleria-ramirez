<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link } from '@inertiajs/vue3';
import Button from 'primevue/button';
import { computed } from 'vue';

const props = defineProps({
    entries: Object,
});

function itemCount(entry) {
    return entry.items.reduce((sum, item) => sum + item.quantity, 0);
}

function totalCost(entry) {
    return entry.items.reduce((sum, item) => sum + (Number(item.unit_cost ?? 0) * item.quantity), 0);
}
</script>

<template>
    <AdminLayout title="Abastecimiento">
        <template #header>
            <h1 class="text-lg font-semibold text-stone-900">Abastecimiento</h1>
            <p class="text-sm text-stone-500">Ingresos de mercancía registrados por proveedor.</p>
        </template>

        <template #actions>
            <Link :href="route('admin.stock-entries.create')">
                <Button label="Registrar ingreso" icon="pi pi-plus" />
            </Link>
        </template>

        <p v-if="entries.data.length === 0" class="rounded-lg border border-stone-200 bg-white p-10 text-center text-stone-500">
            Aún no hay ingresos registrados.
        </p>

        <template v-else>
            <!-- Mobile: stacked cards -->
            <ul class="space-y-3 sm:hidden">
                <li v-for="entry in entries.data" :key="entry.id" class="rounded-lg border border-stone-200 bg-white p-4">
                    <div class="flex items-center justify-between">
                        <span class="font-medium text-stone-900">{{ entry.supplier?.name }}</span>
                        <span class="text-xs text-stone-500">{{ entry.received_at }}</span>
                    </div>
                    <p class="mt-1 text-sm text-stone-500">Ref: {{ entry.reference || '—' }} · {{ itemCount(entry) }} piezas</p>
                    <div class="mt-3 flex items-center justify-between border-t border-stone-100 pt-3 text-sm">
                        <span class="text-stone-500">{{ entry.received_by_user?.name || '—' }}</span>
                        <span class="font-semibold text-accent-700">Q {{ totalCost(entry).toFixed(2) }}</span>
                    </div>
                </li>
            </ul>

            <!-- Desktop: table -->
            <div class="hidden overflow-hidden rounded-lg border border-stone-200 bg-white sm:block">
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-stone-200">
                    <thead class="bg-stone-50">
                        <tr class="text-left text-xs font-semibold uppercase tracking-wide text-stone-500">
                            <th class="px-6 py-3">Fecha</th>
                            <th class="px-6 py-3">Proveedor</th>
                            <th class="px-6 py-3">Referencia</th>
                            <th class="px-6 py-3">Piezas</th>
                            <th class="px-6 py-3">Costo total</th>
                            <th class="px-6 py-3">Recibido por</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100 text-sm text-stone-700">
                        <tr v-for="entry in entries.data" :key="entry.id">
                            <td class="px-6 py-3">{{ entry.received_at }}</td>
                            <td class="px-6 py-3 font-medium text-stone-900">{{ entry.supplier?.name }}</td>
                            <td class="px-6 py-3">{{ entry.reference || '—' }}</td>
                            <td class="px-6 py-3">{{ itemCount(entry) }}</td>
                            <td class="px-6 py-3">Q {{ totalCost(entry).toFixed(2) }}</td>
                            <td class="px-6 py-3">{{ entry.received_by_user?.name || '—' }}</td>
                        </tr>
                    </tbody>
                </table>
              </div>
            </div>
        </template>

        <div class="mt-4 flex flex-wrap gap-1">
            <Link
                v-for="(link, index) in entries.links"
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
    </AdminLayout>
</template>
