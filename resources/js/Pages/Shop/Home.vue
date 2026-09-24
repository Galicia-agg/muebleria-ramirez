<script setup>
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import InputNumber from 'primevue/inputnumber';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import { ref } from 'vue';

defineProps({
    featuredProducts: Array,
    categories: Array,
});

const addForm = useForm({ product_id: null, quantity: 1 });

function quickAdd(product) {
    addForm.product_id = product.id;
    addForm.quantity = 1;
    addForm.post(route('cart.store'), { preserveScroll: true });
}

const search = ref('');
const sort = ref('');
const minPrice = ref(null);
const maxPrice = ref(null);

const sortOptions = [
    { label: 'Recomendados', value: '' },
    { label: 'Más nuevos', value: 'newest' },
    { label: 'Precio: menor a mayor', value: 'price_asc' },
    { label: 'Precio: mayor a menor', value: 'price_desc' },
];

function goToCatalog() {
    router.get(route('catalog.index'), {
        search: search.value || undefined,
        sort: sort.value || undefined,
        min_price: minPrice.value || undefined,
        max_price: maxPrice.value || undefined,
    });
}
</script>

<template>
    <ShopLayout>
        <section class="bg-primary-900 py-16 text-white">
            <div class="mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
                <span class="inline-block rounded-full bg-accent-500/20 px-4 py-1 text-xs font-semibold uppercase tracking-wide text-accent-300">
                    Calidad y estilo
                </span>
                <h1 class="mt-4 text-3xl font-bold sm:text-4xl">Muebles que transforman tu hogar</h1>
                <p class="mt-3 text-primary-100">Calidad y diseño guatemalteco para sala, comedor, dormitorio y más.</p>
                <Link :href="route('catalog.index')">
                    <Button label="Ver catálogo" class="mt-6 !border-accent-500 !bg-accent-500 hover:!bg-accent-600" size="large" />
                </Link>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-wrap items-center gap-3">
                <InputText v-model="search" placeholder="Buscar productos..." class="w-full sm:w-64" @keyup.enter="goToCatalog" />
                <Select v-model="sort" :options="sortOptions" optionLabel="label" optionValue="value" placeholder="Ordenar" class="flex-1 sm:flex-none" @change="goToCatalog" />
                <Button label="Buscar" size="small" @click="goToCatalog" />
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
                <aside class="space-y-2">
                    <h3 class="text-sm font-semibold text-stone-800">Categorías</h3>
                    <Link
                        :href="route('catalog.index')"
                        class="block text-left text-sm text-stone-600 hover:text-primary-700"
                    >
                        Todas
                    </Link>
                    <Link
                        v-for="category in categories"
                        :key="category.id"
                        :href="route('catalog.index', { category: category.slug })"
                        class="block text-left text-sm text-stone-600 hover:text-primary-700"
                    >
                        {{ category.name }}
                    </Link>

                    <h3 class="pt-4 text-sm font-semibold text-stone-800">Precio (Q)</h3>
                    <div class="flex items-center gap-2">
                        <InputNumber v-model="minPrice" placeholder="Mín" class="w-full" inputClass="w-full" :min="0" />
                        <span class="text-stone-400">–</span>
                        <InputNumber v-model="maxPrice" placeholder="Máx" class="w-full" inputClass="w-full" :min="0" />
                    </div>
                    <Button label="Aplicar precio" size="small" outlined class="w-full" @click="goToCatalog" />
                </aside>

                <div class="lg:col-span-3">
                    <h2 class="mb-4 text-lg font-semibold text-primary-900">Destacados</h2>
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-6">
                        <div
                            v-for="product in featuredProducts"
                            :key="product.id"
                            class="group overflow-hidden rounded-xl border border-primary-100 bg-white transition hover:shadow-md"
                        >
                            <Link :href="route('catalog.show', product.slug)" class="relative block aspect-square overflow-hidden bg-primary-50">
                                <img
                                    v-if="product.images?.[0]"
                                    :src="product.images[0].url"
                                    :alt="product.name"
                                    class="h-full w-full object-cover transition duration-200 group-hover:scale-105"
                                />
                                <div v-else class="flex h-full w-full items-center justify-center text-stone-400">
                                    Sin imagen
                                </div>
                                <span
                                    v-if="product.compare_at_price"
                                    class="absolute left-2 top-2 rounded-full bg-accent-600 px-2 py-0.5 text-xs font-bold text-white"
                                >
                                    -{{ Math.round((1 - product.price / product.compare_at_price) * 100) }}%
                                </span>
                            </Link>
                            <div class="p-2.5 sm:p-3">
                                <Link :href="route('catalog.show', product.slug)" class="line-clamp-2 text-sm font-medium text-stone-800 hover:text-accent-700">
                                    {{ product.name }}
                                </Link>
                                <div class="mt-1 flex items-baseline gap-1.5">
                                    <p class="text-sm font-bold text-accent-700 sm:text-base">Q {{ product.price }}</p>
                                    <p v-if="product.compare_at_price" class="text-xs text-stone-400 line-through">
                                        Q {{ product.compare_at_price }}
                                    </p>
                                </div>
                                <Button label="Agregar" size="small" class="mt-2 w-full !text-xs sm:!text-sm" @click="quickAdd(product)" />
                            </div>
                        </div>

                        <p v-if="featuredProducts.length === 0" class="col-span-full text-center text-stone-500">
                            Aún no hay productos destacados.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </ShopLayout>
</template>
