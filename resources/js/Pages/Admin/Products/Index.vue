<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import Button from 'primevue/button';

defineProps({
    products: Object,
});

function destroy(product) {
    if (confirm(`¿Desactivar "${product.name}"?`)) {
        router.delete(route('admin.products.destroy', product.id));
    }
}
</script>

<template>
    <AdminLayout title="Productos">
        <template #header>
            <h1 class="text-lg font-semibold text-stone-900">Productos</h1>
        </template>

        <template #actions>
            <Link :href="route('admin.products.create')">
                <Button label="Nuevo producto" icon="pi pi-plus" />
            </Link>
        </template>

        <p v-if="products.data.length === 0" class="rounded-lg border border-stone-200 bg-white p-10 text-center text-stone-500">
            No hay productos.
        </p>

        <template v-else>
            <!-- Mobile: stacked cards -->
            <ul class="space-y-3 sm:hidden">
                <li v-for="product in products.data" :key="product.id" class="rounded-lg border border-stone-200 bg-white p-4">
                    <div class="flex items-start gap-3">
                        <img
                            v-if="product.images?.[0]"
                            :src="product.images[0].url"
                            class="h-14 w-14 shrink-0 rounded object-cover"
                        />
                        <div v-else class="flex h-14 w-14 shrink-0 items-center justify-center rounded bg-stone-100 text-stone-400">
                            <i class="pi pi-image" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-medium text-stone-900">{{ product.name }}</p>
                            <p class="text-xs text-stone-500">SKU {{ product.sku }} · {{ product.category?.name }}</p>
                            <div class="mt-1 flex items-center gap-2">
                                <span class="font-semibold text-accent-700">Q {{ product.price }}</span>
                                <span
                                    class="rounded-full px-2 py-0.5 text-xs"
                                    :class="product.active ? 'bg-green-100 text-green-700' : 'bg-stone-100 text-stone-500'"
                                >
                                    {{ product.active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center justify-between border-t border-stone-100 pt-3 text-sm">
                        <span :class="product.stock === 0 ? 'font-semibold text-red-600' : 'text-stone-600'">
                            Stock: {{ product.stock }}
                        </span>
                        <div class="flex gap-4">
                            <Link :href="route('admin.products.edit', product.id)" class="font-medium text-primary-600">Editar</Link>
                            <button class="font-medium text-red-600" @click="destroy(product)">Desactivar</button>
                        </div>
                    </div>
                </li>
            </ul>

            <!-- Desktop: table -->
            <div class="hidden overflow-hidden rounded-lg border border-stone-200 bg-white sm:block">
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-stone-200">
                    <thead class="bg-stone-50">
                        <tr class="text-left text-xs font-semibold uppercase tracking-wide text-stone-500">
                            <th class="px-6 py-3"></th>
                            <th class="px-6 py-3">SKU</th>
                            <th class="px-6 py-3">Nombre</th>
                            <th class="px-6 py-3">Categoría</th>
                            <th class="px-6 py-3">Precio</th>
                            <th class="px-6 py-3">Stock</th>
                            <th class="px-6 py-3">Estado</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100 text-sm text-stone-700">
                        <tr v-for="product in products.data" :key="product.id">
                            <td class="px-6 py-3">
                                <img
                                    v-if="product.images?.[0]"
                                    :src="product.images[0].url"
                                    class="h-10 w-10 rounded object-cover"
                                />
                            </td>
                            <td class="px-6 py-3">{{ product.sku }}</td>
                            <td class="px-6 py-3 font-medium text-stone-900">{{ product.name }}</td>
                            <td class="px-6 py-3">{{ product.category?.name }}</td>
                            <td class="px-6 py-3">Q {{ product.price }}</td>
                            <td class="px-6 py-3" :class="{ 'font-semibold text-red-600': product.stock === 0 }">
                                {{ product.stock }}
                            </td>
                            <td class="px-6 py-3">
                                <span
                                    class="rounded-full px-2 py-0.5 text-xs"
                                    :class="product.active ? 'bg-green-100 text-green-700' : 'bg-stone-100 text-stone-500'"
                                >
                                    {{ product.active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <Link :href="route('admin.products.edit', product.id)" class="mr-3 font-medium text-primary-600 hover:text-primary-800">
                                    Editar
                                </Link>
                                <button class="font-medium text-red-600 hover:text-red-800" @click="destroy(product)">
                                    Desactivar
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
              </div>
            </div>
        </template>

        <div class="mt-4 flex flex-wrap gap-1">
            <Link
                v-for="(link, index) in products.links"
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
