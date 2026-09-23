<script setup>
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import InputNumber from 'primevue/inputnumber';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import { ref } from 'vue';

const props = defineProps({
    products: Object,
    categories: Array,
    filters: Object,
});

const search = ref(props.filters.search ?? '');
const sort = ref(props.filters.sort ?? '');
const minPrice = ref(props.filters.min_price ?? null);
const maxPrice = ref(props.filters.max_price ?? null);

const sortOptions = [
    { label: 'Recomendados', value: '' },
    { label: 'Más nuevos', value: 'newest' },
    { label: 'Precio: menor a mayor', value: 'price_asc' },
    { label: 'Precio: mayor a menor', value: 'price_desc' },
];

function applyFilters() {
    router.get(route('catalog.index'), {
        ...props.filters,
        search: search.value,
        sort: sort.value,
        min_price: minPrice.value || undefined,
        max_price: maxPrice.value || undefined,
    }, { preserveState: true, replace: true });
}

function filterByCategory(slug) {
    router.get(route('catalog.index'), { ...props.filters, category: slug || undefined }, { preserveState: true });
}

const addForm = useForm({ product_id: null, quantity: 1 });

function quickAdd(product) {
    addForm.product_id = product.id;
    addForm.quantity = 1;
    addForm.post(route('cart.store'), { preserveScroll: true });
}
</script>

<template>
    <ShopLayout title="Catálogo">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-wrap items-center gap-3">
                <InputText v-model="search" placeholder="Buscar productos..." class="w-64" @keyup.enter="applyFilters" />
                <Select v-model="sort" :options="sortOptions" optionLabel="label" optionValue="value" placeholder="Ordenar" @change="applyFilters" />
                <Button label="Buscar" size="small" @click="applyFilters" />
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
                <aside class="space-y-2">
                    <h3 class="text-sm font-semibold text-stone-800">Categorías</h3>
                    <button
                        class="block text-left text-sm text-stone-600 hover:text-primary-700"
                        :class="{ 'font-semibold text-primary-700': !filters.category }"
                        @click="filterByCategory(null)"
                    >
                        Todas
                    </button>
                    <button
                        v-for="category in categories"
                        :key="category.id"
                        class="block text-left text-sm text-stone-600 hover:text-primary-700"
                        :class="{ 'font-semibold text-primary-700': filters.category === category.slug }"
                        @click="filterByCategory(category.slug)"
                    >
                        {{ category.name }}
                    </button>

                    <h3 class="pt-4 text-sm font-semibold text-stone-800">Precio (Q)</h3>
                    <div class="flex items-center gap-2">
                        <InputNumber v-model="minPrice" placeholder="Mín" class="w-full" inputClass="w-full" :min="0" />
                        <span class="text-stone-400">–</span>
                        <InputNumber v-model="maxPrice" placeholder="Máx" class="w-full" inputClass="w-full" :min="0" />
                    </div>
                    <Button label="Aplicar precio" size="small" outlined class="w-full" @click="applyFilters" />
                </aside>

                <div class="grid grid-cols-2 gap-6 sm:grid-cols-3 lg:col-span-3">
                    <div
                        v-for="product in products.data"
                        :key="product.id"
                        class="overflow-hidden rounded-lg border border-stone-200 bg-white"
                    >
                        <Link :href="route('catalog.show', product.slug)">
                            <img
                                v-if="product.images?.[0]"
                                :src="product.images[0].url"
                                :alt="product.name"
                                class="h-40 w-full object-cover"
                            />
                            <div v-else class="flex h-40 w-full items-center justify-center bg-stone-100 text-stone-400">
                                Sin imagen
                            </div>
                        </Link>
                        <div class="p-3">
                            <Link :href="route('catalog.show', product.slug)" class="text-sm font-medium text-stone-800 hover:text-primary-700">
                                {{ product.name }}
                            </Link>
                            <p class="mt-1 text-sm font-semibold text-accent-700">Q {{ product.price }}</p>
                            <Button label="Agregar" size="small" class="mt-2 w-full" @click="quickAdd(product)" />
                        </div>
                    </div>

                    <p v-if="products.data.length === 0" class="col-span-full py-10 text-center text-stone-500">
                        No se encontraron productos.
                    </p>
                </div>
            </div>

            <div class="mt-8 flex flex-wrap justify-center gap-1">
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
        </div>
    </ShopLayout>
</template>
