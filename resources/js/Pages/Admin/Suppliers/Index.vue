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
    suppliers: Array,
});

const editingId = ref(null);

const form = useForm({
    name: '',
    contact_name: '',
    phone: '',
    email: '',
    address: '',
    notes: '',
    active: true,
});

function submit() {
    if (editingId.value) {
        form.put(route('admin.suppliers.update', editingId.value), { onSuccess: () => cancelEdit() });
    } else {
        form.post(route('admin.suppliers.store'), { onSuccess: () => form.reset() });
    }
}

function edit(supplier) {
    editingId.value = supplier.id;
    form.name = supplier.name;
    form.contact_name = supplier.contact_name ?? '';
    form.phone = supplier.phone ?? '';
    form.email = supplier.email ?? '';
    form.address = supplier.address ?? '';
    form.notes = supplier.notes ?? '';
    form.active = supplier.active;
}

function cancelEdit() {
    editingId.value = null;
    form.reset();
}

function destroy(supplier) {
    if (confirm(`¿Eliminar al proveedor "${supplier.name}"?`)) {
        router.delete(route('admin.suppliers.destroy', supplier.id));
    }
}
</script>

<template>
    <AdminLayout title="Proveedores">
        <template #header>
            <h1 class="text-lg font-semibold text-stone-900">Proveedores</h1>
        </template>

        <div class="grid gap-6 md:grid-cols-3">
            <div class="overflow-hidden rounded-lg border border-stone-200 bg-white md:col-span-2">
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-stone-200">
                    <thead class="bg-stone-50">
                        <tr class="text-left text-xs font-semibold uppercase tracking-wide text-stone-500">
                            <th class="px-6 py-3">Nombre</th>
                            <th class="px-6 py-3">Contacto</th>
                            <th class="px-6 py-3">Teléfono</th>
                            <th class="px-6 py-3">Estado</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100 text-sm text-stone-700">
                        <tr v-for="supplier in suppliers" :key="supplier.id">
                            <td class="px-6 py-3 font-medium text-stone-900">{{ supplier.name }}</td>
                            <td class="px-6 py-3">{{ supplier.contact_name || '—' }}</td>
                            <td class="px-6 py-3">{{ supplier.phone || '—' }}</td>
                            <td class="px-6 py-3">
                                <span
                                    class="rounded-full px-2 py-0.5 text-xs"
                                    :class="supplier.active ? 'bg-green-100 text-green-700' : 'bg-stone-100 text-stone-500'"
                                >
                                    {{ supplier.active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <button class="mr-3 font-medium text-primary-600 hover:text-primary-800" @click="edit(supplier)">Editar</button>
                                <button class="font-medium text-red-600 hover:text-red-800" @click="destroy(supplier)">Eliminar</button>
                            </td>
                        </tr>
                        <tr v-if="suppliers.length === 0">
                            <td colspan="5" class="px-6 py-10 text-center text-stone-500">No hay proveedores.</td>
                        </tr>
                    </tbody>
                </table>
              </div>
            </div>

            <div class="rounded-lg border border-stone-200 bg-white p-6">
                <h3 class="mb-4 text-sm font-semibold text-stone-900">
                    {{ editingId ? 'Editar proveedor' : 'Nuevo proveedor' }}
                </h3>
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <InputLabel value="Nombre" />
                        <TextInput v-model="form.name" class="mt-1 block w-full" />
                        <InputError :message="form.errors.name" class="mt-1" />
                    </div>
                    <div>
                        <InputLabel value="Persona de contacto" />
                        <TextInput v-model="form.contact_name" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <InputLabel value="Teléfono" />
                        <TextInput v-model="form.phone" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <InputLabel value="Email" />
                        <TextInput v-model="form.email" type="email" class="mt-1 block w-full" />
                        <InputError :message="form.errors.email" class="mt-1" />
                    </div>
                    <div>
                        <InputLabel value="Dirección" />
                        <textarea v-model="form.address" rows="2" class="mt-1 block w-full rounded-md border-stone-300 text-sm shadow-sm"></textarea>
                    </div>
                    <div>
                        <InputLabel value="Notas" />
                        <textarea v-model="form.notes" rows="2" class="mt-1 block w-full rounded-md border-stone-300 text-sm shadow-sm"></textarea>
                    </div>
                    <div class="flex items-center gap-2">
                        <Checkbox v-model:checked="form.active" />
                        <InputLabel value="Activo" />
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
