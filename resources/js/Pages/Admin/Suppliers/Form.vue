<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue';
import { Link, useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';

const props = defineProps({
    supplier: Object,
});

const isEditing = !!props.supplier;

const form = useForm({
    name: props.supplier?.name ?? '',
    contact_name: props.supplier?.contact_name ?? '',
    phone: props.supplier?.phone ?? '',
    email: props.supplier?.email ?? '',
    address: props.supplier?.address ?? '',
    notes: props.supplier?.notes ?? '',
    active: props.supplier?.active ?? true,
});

function submit() {
    if (isEditing) {
        form.put(route('admin.suppliers.update', props.supplier.id));
    } else {
        form.post(route('admin.suppliers.store'));
    }
}
</script>

<template>
    <AdminLayout :title="isEditing ? 'Editar proveedor' : 'Nuevo proveedor'">
        <template #header>
            <h1 class="text-lg font-semibold text-stone-900">
                {{ isEditing ? 'Editar proveedor' : 'Nuevo proveedor' }}
            </h1>
        </template>

        <div class="max-w-xl">
            <Link
                :href="route('admin.suppliers.index')"
                class="mb-3 inline-flex items-center gap-1.5 text-sm font-medium text-stone-500 hover:text-primary-700"
            >
                <i class="pi pi-arrow-left text-xs" /> Volver a proveedores
            </Link>

            <div class="rounded-lg border border-stone-200 bg-white p-6">
            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <InputLabel value="Nombre" />
                    <TextInput v-model="form.name" class="mt-1 block w-full" autofocus />
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
                    <Button type="submit" label="Guardar" :loading="form.processing" />
                    <Link :href="route('admin.suppliers.index')" class="inline-flex items-center text-sm text-stone-500 hover:text-stone-700">
                        Cancelar
                    </Link>
                </div>
            </form>
            </div>
        </div>
    </AdminLayout>
</template>
