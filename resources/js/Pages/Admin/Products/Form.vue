<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue';
import { router, useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import { computed, ref } from 'vue';

const props = defineProps({
    categories: Array,
    product: Object,
    maxImages: Number,
});

const isEditing = !!props.product;

const form = useForm({
    category_id: props.product?.category_id ?? '',
    sku: props.product?.sku ?? '',
    name: props.product?.name ?? '',
    description: props.product?.description ?? '',
    price: props.product?.price ?? '0.00',
    compare_at_price: props.product?.compare_at_price ?? '',
    stock: props.product?.stock ?? 0,
    material: props.product?.material ?? '',
    color: props.product?.color ?? '',
    width_cm: props.product?.width_cm ?? '',
    height_cm: props.product?.height_cm ?? '',
    depth_cm: props.product?.depth_cm ?? '',
    weight_kg: props.product?.weight_kg ?? '',
    featured: props.product?.featured ?? false,
    active: props.product?.active ?? true,
    images: [],
});

const existingImages = ref(props.product?.images ?? []);
const remainingSlots = computed(() => props.maxImages - existingImages.value.length - form.images.length);

const newImagePreviews = computed(() => form.images.map((file) => URL.createObjectURL(file)));

function onFilesSelected(event) {
    const files = Array.from(event.target.files ?? []);
    form.images = [...form.images, ...files].slice(0, props.maxImages - existingImages.value.length);
    event.target.value = '';
}

function removeNewImage(index) {
    form.images = form.images.filter((_, i) => i !== index);
}

function removeExistingImage(image) {
    if (confirm('¿Eliminar esta imagen?')) {
        router.delete(route('admin.products.images.destroy', image.id), {
            preserveScroll: true,
            onSuccess: () => {
                existingImages.value = existingImages.value.filter((img) => img.id !== image.id);
            },
        });
    }
}

function submit() {
    if (isEditing) {
        form.put(route('admin.products.update', props.product.id), { forceFormData: true });
    } else {
        form.post(route('admin.products.store'), { forceFormData: true });
    }
}
</script>

<template>
    <AdminLayout :title="isEditing ? 'Editar producto' : 'Nuevo producto'">
        <template #header>
            <h1 class="text-lg font-semibold text-stone-900">
                {{ isEditing ? 'Editar producto' : 'Nuevo producto' }}
            </h1>
        </template>

        <div class="max-w-3xl rounded-lg border border-stone-200 bg-white p-6">
            <form @submit.prevent="submit" class="space-y-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <InputLabel value="Categoría" />
                        <select v-model="form.category_id" class="mt-1 block w-full rounded-md border-stone-300 text-sm shadow-sm">
                            <option value="" disabled>Selecciona una categoría</option>
                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                {{ category.name }}
                            </option>
                        </select>
                        <InputError :message="form.errors.category_id" class="mt-1" />
                    </div>

                    <div>
                        <InputLabel value="SKU" />
                        <TextInput v-model="form.sku" class="mt-1 block w-full" />
                        <InputError :message="form.errors.sku" class="mt-1" />
                    </div>

                    <div class="sm:col-span-2">
                        <InputLabel value="Nombre" />
                        <TextInput v-model="form.name" class="mt-1 block w-full" />
                        <InputError :message="form.errors.name" class="mt-1" />
                    </div>

                    <div class="sm:col-span-2">
                        <InputLabel value="Descripción" />
                        <textarea v-model="form.description" rows="3" class="mt-1 block w-full rounded-md border-stone-300 text-sm shadow-sm"></textarea>
                    </div>

                    <div>
                        <InputLabel value="Precio" />
                        <TextInput v-model="form.price" type="number" step="0.01" class="mt-1 block w-full" />
                        <InputError :message="form.errors.price" class="mt-1" />
                    </div>

                    <div>
                        <InputLabel value="Precio antes de descuento (opcional)" />
                        <TextInput v-model="form.compare_at_price" type="number" step="0.01" class="mt-1 block w-full" />
                        <InputError :message="form.errors.compare_at_price" class="mt-1" />
                    </div>

                    <div>
                        <InputLabel value="Stock" />
                        <TextInput v-model="form.stock" type="number" class="mt-1 block w-full" />
                        <InputError :message="form.errors.stock" class="mt-1" />
                    </div>

                    <div>
                        <InputLabel value="Material" />
                        <TextInput v-model="form.material" class="mt-1 block w-full" />
                    </div>

                    <div>
                        <InputLabel value="Color" />
                        <TextInput v-model="form.color" class="mt-1 block w-full" />
                    </div>

                    <div class="grid grid-cols-3 gap-2 sm:col-span-2">
                        <div>
                            <InputLabel value="Ancho (cm)" />
                            <TextInput v-model="form.width_cm" type="number" step="0.1" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <InputLabel value="Alto (cm)" />
                            <TextInput v-model="form.height_cm" type="number" step="0.1" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <InputLabel value="Profundidad (cm)" />
                            <TextInput v-model="form.depth_cm" type="number" step="0.1" class="mt-1 block w-full" />
                        </div>
                    </div>

                    <div>
                        <InputLabel value="Peso (kg)" />
                        <TextInput v-model="form.weight_kg" type="number" step="0.01" class="mt-1 block w-full" />
                    </div>

                    <div class="flex items-center gap-2">
                        <Checkbox v-model:checked="form.featured" />
                        <InputLabel value="Producto destacado" />
                    </div>

                    <div class="flex items-center gap-2">
                        <Checkbox v-model:checked="form.active" />
                        <InputLabel value="Activo" />
                    </div>
                </div>

                <div>
                    <InputLabel :value="`Fotografías (máximo ${maxImages})`" />

                    <div class="mt-2 flex flex-wrap gap-3">
                        <div v-for="image in existingImages" :key="image.id" class="relative h-24 w-24">
                            <img :src="image.url" class="h-full w-full rounded object-cover" />
                            <button
                                type="button"
                                class="absolute -right-2 -top-2 flex h-6 w-6 items-center justify-center rounded-full bg-red-600 text-xs text-white"
                                @click="removeExistingImage(image)"
                            >
                                ×
                            </button>
                        </div>

                        <div v-for="(preview, index) in newImagePreviews" :key="preview" class="relative h-24 w-24">
                            <img :src="preview" class="h-full w-full rounded object-cover" />
                            <button
                                type="button"
                                class="absolute -right-2 -top-2 flex h-6 w-6 items-center justify-center rounded-full bg-red-600 text-xs text-white"
                                @click="removeNewImage(index)"
                            >
                                ×
                            </button>
                        </div>
                    </div>

                    <input
                        v-if="remainingSlots > 0"
                        type="file"
                        accept="image/*"
                        multiple
                        class="mt-3 block text-sm"
                        @change="onFilesSelected"
                    />
                    <p v-else class="mt-3 text-sm text-stone-500">Alcanzaste el máximo de {{ maxImages }} fotografías.</p>
                    <InputError :message="form.errors.images" class="mt-2" />
                </div>

                <Button type="submit" label="Guardar" :disabled="form.processing" />
            </form>
        </div>
    </AdminLayout>
</template>
