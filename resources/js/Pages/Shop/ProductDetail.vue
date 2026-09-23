<script setup>
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import InputNumber from 'primevue/inputnumber';
import { ref } from 'vue';

const props = defineProps({
    product: Object,
});

const activeImage = ref(props.product.images?.[0]?.url ?? null);

const form = useForm({
    product_id: props.product.id,
    quantity: 1,
});

function addToCart() {
    form.post(route('cart.store'), { preserveScroll: true });
}

const specs = [
    { label: 'Material', value: props.product.material },
    { label: 'Color', value: props.product.color },
    { label: 'Ancho', value: props.product.width_cm ? `${props.product.width_cm} cm` : null },
    { label: 'Alto', value: props.product.height_cm ? `${props.product.height_cm} cm` : null },
    { label: 'Profundidad', value: props.product.depth_cm ? `${props.product.depth_cm} cm` : null },
    { label: 'Peso', value: props.product.weight_kg ? `${props.product.weight_kg} kg` : null },
].filter((spec) => spec.value);
</script>

<template>
    <ShopLayout :title="product.name">
        <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-10 lg:grid-cols-2">
                <div>
                    <div class="aspect-square overflow-hidden rounded-lg border border-stone-200 bg-stone-100">
                        <img v-if="activeImage" :src="activeImage" :alt="product.name" class="h-full w-full object-cover" />
                        <div v-else class="flex h-full items-center justify-center text-stone-400">Sin imagen</div>
                    </div>
                    <div class="mt-3 flex gap-2 overflow-x-auto">
                        <button
                            v-for="image in product.images"
                            :key="image.id"
                            @click="activeImage = image.url"
                            class="h-16 w-16 shrink-0 overflow-hidden rounded border-2"
                            :class="activeImage === image.url ? 'border-primary-600' : 'border-transparent'"
                        >
                            <img :src="image.url" :alt="product.name" class="h-full w-full object-cover" />
                        </button>
                    </div>
                </div>

                <div>
                    <h1 class="text-2xl font-bold text-stone-900">{{ product.name }}</h1>
                    <div class="mt-2 flex items-baseline gap-3">
                        <span class="text-2xl font-semibold text-accent-700">Q {{ product.price }}</span>
                        <span v-if="product.compare_at_price" class="text-stone-400 line-through">
                            Q {{ product.compare_at_price }}
                        </span>
                    </div>

                    <p class="mt-4 whitespace-pre-line text-sm text-stone-600">{{ product.description }}</p>

                    <dl v-if="specs.length" class="mt-6 grid grid-cols-2 gap-3 text-sm">
                        <div v-for="spec in specs" :key="spec.label">
                            <dt class="text-stone-500">{{ spec.label }}</dt>
                            <dd class="font-medium text-stone-800">{{ spec.value }}</dd>
                        </div>
                    </dl>

                    <p class="mt-4 text-sm" :class="product.stock > 0 ? 'text-green-600' : 'text-red-600'">
                        {{ product.stock > 0 ? `${product.stock} disponibles` : 'Agotado' }}
                    </p>

                    <div class="mt-6 flex items-center gap-3">
                        <InputNumber v-model="form.quantity" :min="1" :max="product.stock" showButtons buttonLayout="horizontal" class="w-32" />
                        <Button label="Agregar al carrito" :disabled="product.stock === 0 || form.processing" @click="addToCart" />
                    </div>
                    <p v-if="form.errors.quantity" class="mt-2 text-sm text-red-600">{{ form.errors.quantity }}</p>
                </div>
            </div>
        </div>
    </ShopLayout>
</template>
