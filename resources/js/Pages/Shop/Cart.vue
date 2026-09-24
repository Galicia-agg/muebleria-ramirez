<script setup>
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Image from 'primevue/image';
import InputNumber from 'primevue/inputnumber';
import { computed } from 'vue';

const props = defineProps({
    cart: Object,
});

const page = usePage();
const isAuthenticated = computed(() => !!page.props.auth?.user);

const total = computed(() =>
    props.cart.items.reduce((sum, item) => sum + item.quantity * Number(item.unit_price), 0),
);

function updateQuantity(item, quantity) {
    router.patch(route('cart.update', item.id), { quantity }, { preserveScroll: true });
}

function removeItem(item) {
    router.delete(route('cart.destroy', item.id), { preserveScroll: true });
}
</script>

<template>
    <ShopLayout title="Carrito">
        <div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
            <h1 class="mb-6 text-xl font-bold text-stone-900">Tu carrito</h1>

            <div v-if="cart.items.length === 0" class="rounded-lg border border-stone-200 bg-white p-10 text-center text-stone-500">
                Tu carrito está vacío.
                <Link :href="route('catalog.index')" class="mt-2 block text-primary-700 hover:underline">
                    Ir al catálogo
                </Link>
            </div>

            <div v-else class="space-y-4">
                <div
                    v-for="item in cart.items"
                    :key="item.id"
                    class="flex flex-wrap items-center gap-4 rounded-lg border border-stone-200 bg-white p-4"
                >
                    <Image
                        v-if="item.product.images?.[0]"
                        :src="item.product.images[0].url"
                        :alt="item.product.name"
                        preview
                        class="block h-16 w-16 shrink-0"
                        imageClass="h-16 w-16 rounded object-cover"
                    />
                    <div class="min-w-[140px] flex-1">
                        <p class="font-medium text-stone-800">{{ item.product.name }}</p>
                        <p class="text-sm text-stone-500">Q {{ item.unit_price }} c/u</p>
                    </div>
                    <div class="flex w-full items-center justify-between gap-4 sm:w-auto sm:justify-end">
                        <InputNumber
                            :modelValue="item.quantity"
                            :min="1"
                            :max="item.product.stock"
                            showButtons
                            buttonLayout="horizontal"
                            inputClass="w-10 text-center"
                            :inputStyle="{ width: '2.5rem' }"
                            @update:modelValue="(value) => updateQuantity(item, value)"
                        />
                        <p class="w-24 text-right font-semibold text-stone-800">
                            Q {{ (item.quantity * Number(item.unit_price)).toFixed(2) }}
                        </p>
                        <Button icon="pi pi-trash" severity="danger" text @click="removeItem(item)" />
                    </div>
                </div>

                <div class="flex items-center justify-between rounded-lg border border-stone-200 bg-white p-4">
                    <span class="text-lg font-semibold text-stone-800">Total</span>
                    <span class="text-lg font-bold text-accent-700">Q {{ total.toFixed(2) }}</span>
                </div>

                <div class="flex justify-end">
                    <Link v-if="isAuthenticated" :href="route('checkout.create')">
                        <Button label="Ir a pagar" size="large" />
                    </Link>
                    <Link v-else :href="route('login')">
                        <Button label="Inicia sesión para continuar" size="large" />
                    </Link>
                </div>
            </div>
        </div>
    </ShopLayout>
</template>
