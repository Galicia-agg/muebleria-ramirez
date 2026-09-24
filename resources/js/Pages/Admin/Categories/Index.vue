<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import Button from 'primevue/button';

const props = defineProps({
    categories: Array,
});

function destroy(category) {
    if (confirm(`¿Eliminar la categoría "${category.name}"?`)) {
        router.delete(route('admin.categories.destroy', category.id));
    }
}
</script>

<template>
    <AdminLayout title="Categorías">
        <template #header>
            <h1 class="text-lg font-semibold text-stone-900">Categorías</h1>
        </template>

        <template #actions>
            <Link :href="route('admin.categories.create')">
                <Button label="Nueva categoría" icon="pi pi-plus" />
            </Link>
        </template>

        <p v-if="categories.length === 0" class="rounded-lg border border-stone-200 bg-white p-10 text-center text-stone-500">
            No hay categorías.
        </p>

        <template v-else>
            <!-- Mobile: stacked cards -->
            <ul class="space-y-3 sm:hidden">
                <li v-for="category in categories" :key="category.id" class="rounded-lg border border-stone-200 bg-white p-4">
                    <div class="flex items-center justify-between">
                        <span class="font-medium text-stone-900">{{ category.name }}</span>
                        <span
                            class="rounded-full px-2 py-0.5 text-xs"
                            :class="category.active ? 'bg-green-100 text-green-700' : 'bg-stone-100 text-stone-500'"
                        >
                            {{ category.active ? 'Activa' : 'Inactiva' }}
                        </span>
                    </div>
                    <p class="mt-1 text-sm text-stone-500">
                        Padre: {{ categories.find((c) => c.id === category.parent_id)?.name ?? '—' }}
                    </p>
                    <div class="mt-3 flex gap-4 border-t border-stone-100 pt-3 text-sm">
                        <Link :href="route('admin.categories.edit', category.id)" class="font-medium text-primary-600">Editar</Link>
                        <button class="font-medium text-red-600" @click="destroy(category)">Eliminar</button>
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
                            <th class="px-6 py-3">Categoría padre</th>
                            <th class="px-6 py-3">Estado</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100 text-sm text-stone-700">
                        <tr v-for="category in categories" :key="category.id">
                            <td class="px-6 py-3 font-medium text-stone-900">{{ category.name }}</td>
                            <td class="px-6 py-3">
                                {{ categories.find((c) => c.id === category.parent_id)?.name ?? '—' }}
                            </td>
                            <td class="px-6 py-3">{{ category.active ? 'Activa' : 'Inactiva' }}</td>
                            <td class="px-6 py-3 text-right">
                                <Link :href="route('admin.categories.edit', category.id)" class="mr-3 font-medium text-primary-600 hover:text-primary-800">Editar</Link>
                                <button class="font-medium text-red-600 hover:text-red-800" @click="destroy(category)">Eliminar</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
              </div>
            </div>
        </template>
    </AdminLayout>
</template>
