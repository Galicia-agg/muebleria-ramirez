<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue';
import { Link, useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';

const props = defineProps({
    categories: Array,
    category: Object,
});

const isEditing = !!props.category;

const form = useForm({
    name: props.category?.name ?? '',
    parent_id: props.category?.parent_id ?? '',
    description: props.category?.description ?? '',
    active: props.category?.active ?? true,
});

function submit() {
    if (isEditing) {
        form.put(route('admin.categories.update', props.category.id));
    } else {
        form.post(route('admin.categories.store'));
    }
}
</script>

<template>
    <AdminLayout :title="isEditing ? 'Editar categoría' : 'Nueva categoría'">
        <template #header>
            <h1 class="text-lg font-semibold text-stone-900">
                {{ isEditing ? 'Editar categoría' : 'Nueva categoría' }}
            </h1>
        </template>

        <div class="max-w-xl">
            <Link
                :href="route('admin.categories.index')"
                class="mb-3 inline-flex items-center gap-1.5 text-sm font-medium text-stone-500 hover:text-primary-700"
            >
                <i class="pi pi-arrow-left text-xs" /> Volver a categorías
            </Link>

            <div class="rounded-lg border border-stone-200 bg-white p-6">
            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <InputLabel value="Nombre" />
                    <TextInput v-model="form.name" class="mt-1 block w-full" autofocus />
                    <InputError :message="form.errors.name" class="mt-1" />
                </div>
                <div>
                    <InputLabel value="Categoría padre (opcional)" />
                    <select v-model="form.parent_id" class="mt-1 block w-full rounded-md border-stone-300 text-sm shadow-sm">
                        <option value="">Ninguna</option>
                        <option
                            v-for="parentOption in categories"
                            :key="parentOption.id"
                            :value="parentOption.id"
                            :disabled="parentOption.id === category?.id"
                        >
                            {{ parentOption.name }}
                        </option>
                    </select>
                </div>
                <div>
                    <InputLabel value="Descripción" />
                    <textarea v-model="form.description" rows="3" class="mt-1 block w-full rounded-md border-stone-300 text-sm shadow-sm"></textarea>
                </div>
                <div class="flex items-center gap-2">
                    <Checkbox v-model:checked="form.active" />
                    <InputLabel value="Activa" />
                </div>
                <div class="flex gap-3">
                    <Button type="submit" label="Guardar" :loading="form.processing" />
                    <Link :href="route('admin.categories.index')" class="inline-flex items-center text-sm text-stone-500 hover:text-stone-700">
                        Cancelar
                    </Link>
                </div>
            </form>
            </div>
        </div>
    </AdminLayout>
</template>
