<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Image from 'primevue/image';
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

const isDraggingFiles = ref(false);
let dragCounter = 0;

function addFiles(fileList) {
    const files = Array.from(fileList ?? []).filter((file) => file.type.startsWith('image/'));
    form.images = [...form.images, ...files].slice(0, props.maxImages - existingImages.value.length);
}

function onFilesSelected(event) {
    addFiles(event.target.files);
    event.target.value = '';
}

function onDragEnter() {
    dragCounter++;
    isDraggingFiles.value = true;
}

function onDragLeave() {
    dragCounter--;
    if (dragCounter <= 0) {
        dragCounter = 0;
        isDraggingFiles.value = false;
    }
}

function onDrop(event) {
    dragCounter = 0;
    isDraggingFiles.value = false;
    addFiles(event.dataTransfer?.files);
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

        <div class="max-w-3xl">
            <Link
                :href="route('admin.products.index')"
                class="mb-3 inline-flex items-center gap-1.5 text-sm font-medium text-stone-500 hover:text-primary-700"
            >
                <i class="pi pi-arrow-left text-xs" /> Volver a productos
            </Link>

            <div class="rounded-lg border border-stone-200 bg-white p-6">
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

                    <div v-if="existingImages.length || newImagePreviews.length" class="mt-2 flex flex-wrap gap-3">
                        <div v-for="image in existingImages" :key="image.id" class="relative h-24 w-24 overflow-hidden rounded border border-stone-200 bg-stone-100">
                            <Image :src="image.url" preview class="block h-full w-full" imageClass="h-full w-full object-contain" />
                            <button
                                type="button"
                                class="absolute -right-2 -top-2 z-10 flex h-6 w-6 items-center justify-center rounded-full bg-red-600 text-xs text-white"
                                @click="removeExistingImage(image)"
                            >
                                ×
                            </button>
                        </div>

                        <div v-for="(preview, index) in newImagePreviews" :key="preview" class="relative h-24 w-24 overflow-hidden rounded border border-stone-200 bg-stone-100">
                            <Image :src="preview" preview class="block h-full w-full" imageClass="h-full w-full object-contain" />
                            <button
                                type="button"
                                class="absolute -right-2 -top-2 z-10 flex h-6 w-6 items-center justify-center rounded-full bg-red-600 text-xs text-white"
                                @click="removeNewImage(index)"
                            >
                                ×
                            </button>
                        </div>
                    </div>

                    <label
                        v-if="remainingSlots > 0"
                        class="mt-3 flex cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed px-4 py-6 text-center transition"
                        :class="isDraggingFiles ? 'border-accent-500 bg-accent-50' : 'border-stone-300 hover:border-stone-400'"
                        @dragover.prevent
                        @dragenter.prevent="onDragEnter"
                        @dragleave.prevent="onDragLeave"
                        @drop.prevent="onDrop"
                    >
                        <i class="pi pi-cloud-upload text-2xl text-stone-400" />
                        <p class="mt-2 text-sm text-stone-600">
                            <span class="font-medium text-primary-700">Elige archivos</span> o arrástralos aquí
                        </p>
                        <p class="mt-1 text-xs text-stone-400">Puedes agregar {{ remainingSlots }} foto{{ remainingSlots === 1 ? '' : 's' }} más</p>
                        <input type="file" accept="image/*" multiple class="hidden" @change="onFilesSelected" />
                    </label>
                    <p v-else class="mt-3 text-sm text-stone-500">Alcanzaste el máximo de {{ maxImages }} fotografías.</p>
                    <InputError :message="form.errors.images" class="mt-2" />
                </div>

                <div class="flex gap-3">
                    <Button type="submit" label="Guardar" :loading="form.processing" />
                    <Link :href="route('admin.products.index')" class="inline-flex items-center text-sm text-stone-500 hover:text-stone-700">
                        Cancelar
                    </Link>
                </div>
            </form>
            </div>
        </div>
    </AdminLayout>
</template>
