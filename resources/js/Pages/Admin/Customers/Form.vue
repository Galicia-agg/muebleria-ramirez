<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';

const props = defineProps({
    customer: Object,
});

const isEditing = !!props.customer;

const form = useForm({
    name: props.customer?.name ?? '',
    email: props.customer?.email ?? '',
});

function submit() {
    if (isEditing) {
        form.put(route('admin.customers.update', props.customer.id));
    } else {
        form.post(route('admin.customers.store'));
    }
}
</script>

<template>
    <AdminLayout :title="isEditing ? 'Editar cliente' : 'Nuevo cliente'">
        <template #header>
            <h1 class="text-lg font-semibold text-stone-900">
                {{ isEditing ? 'Editar cliente' : 'Nuevo cliente' }}
            </h1>
        </template>

        <div class="max-w-xl">
            <Link
                :href="route('admin.customers.index')"
                class="mb-3 inline-flex items-center gap-1.5 text-sm font-medium text-stone-500 hover:text-primary-700"
            >
                <i class="pi pi-arrow-left text-xs" /> Volver a clientes
            </Link>

            <div class="rounded-lg border border-stone-200 bg-white p-6">
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <InputLabel value="Nombre" />
                        <TextInput v-model="form.name" class="mt-1 block w-full" autofocus />
                        <InputError :message="form.errors.name" class="mt-1" />
                    </div>
                    <div>
                        <InputLabel value="Email" />
                        <TextInput v-model="form.email" type="email" class="mt-1 block w-full" />
                        <InputError :message="form.errors.email" class="mt-1" />
                    </div>
                    <p v-if="!isEditing" class="text-xs text-stone-500">
                        El cliente establece su propia contraseña con "¿Olvidaste tu contraseña?" en el inicio de sesión.
                    </p>
                    <div class="flex gap-3">
                        <Button type="submit" label="Guardar" :loading="form.processing" />
                        <Link :href="route('admin.customers.index')" class="inline-flex items-center text-sm text-stone-500 hover:text-stone-700">
                            Cancelar
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
