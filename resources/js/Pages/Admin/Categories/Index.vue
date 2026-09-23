<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue';
import { router, useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import { ref } from 'vue';

const props = defineProps({
    categories: Array,
});

const editingId = ref(null);

const form = useForm({
    name: '',
    parent_id: '',
    description: '',
    active: true,
});

function submit() {
    if (editingId.value) {
        form.put(route('admin.categories.update', editingId.value), { onSuccess: () => cancelEdit() });
    } else {
        form.post(route('admin.categories.store'), { onSuccess: () => form.reset() });
    }
}

function edit(category) {
    editingId.value = category.id;
    form.name = category.name;
    form.parent_id = category.parent_id ?? '';
    form.description = category.description ?? '';
    form.active = category.active;
}

function cancelEdit() {
    editingId.value = null;
    form.reset();
}

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

        <div class="grid gap-6 md:grid-cols-3">
            <div class="overflow-hidden rounded-lg border border-stone-200 bg-white md:col-span-2">
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
                                <button class="mr-3 font-medium text-primary-600 hover:text-primary-800" @click="edit(category)">Editar</button>
                                <button class="font-medium text-red-600 hover:text-red-800" @click="destroy(category)">Eliminar</button>
                            </td>
                        </tr>
                        <tr v-if="categories.length === 0">
                            <td colspan="4" class="px-6 py-10 text-center text-stone-500">No hay categorías.</td>
                        </tr>
                    </tbody>
                </table>
              </div>
            </div>

            <div class="rounded-lg border border-stone-200 bg-white p-6">
                <h3 class="mb-4 text-sm font-semibold text-stone-900">
                    {{ editingId ? 'Editar categoría' : 'Nueva categoría' }}
                </h3>
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <InputLabel value="Nombre" />
                        <TextInput v-model="form.name" class="mt-1 block w-full" />
                        <InputError :message="form.errors.name" class="mt-1" />
                    </div>
                    <div>
                        <InputLabel value="Categoría padre (opcional)" />
                        <select v-model="form.parent_id" class="mt-1 block w-full rounded-md border-stone-300 text-sm shadow-sm">
                            <option value="">Ninguna</option>
                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                {{ category.name }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <InputLabel value="Descripción" />
                        <textarea v-model="form.description" rows="2" class="mt-1 block w-full rounded-md border-stone-300 text-sm shadow-sm"></textarea>
                    </div>
                    <div class="flex items-center gap-2">
                        <Checkbox v-model:checked="form.active" />
                        <InputLabel value="Activa" />
                    </div>
                    <div class="flex gap-3">
                        <Button type="submit" label="Guardar" :disabled="form.processing" />
                        <button v-if="editingId" type="button" class="text-sm text-stone-500" @click="cancelEdit">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
