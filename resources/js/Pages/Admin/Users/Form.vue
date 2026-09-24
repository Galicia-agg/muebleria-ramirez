<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';

const props = defineProps({
    user: Object,
    roles: Array,
});

const isEditing = !!props.user;

const form = useForm({
    name: props.user?.name ?? '',
    email: props.user?.email ?? '',
    password: '',
    password_confirmation: '',
    role: props.user?.roles?.[0]?.name ?? props.roles[0] ?? '',
});

function submit() {
    if (isEditing) {
        form.transform((data) => (data.password ? data : { ...data, password: null, password_confirmation: null }))
            .put(route('admin.users.update', props.user.id));
    } else {
        form.post(route('admin.users.store'));
    }
}
</script>

<template>
    <AdminLayout :title="isEditing ? 'Editar usuario' : 'Nuevo usuario'">
        <template #header>
            <h1 class="text-lg font-semibold text-stone-900">
                {{ isEditing ? 'Editar usuario' : 'Nuevo usuario' }}
            </h1>
        </template>

        <div class="max-w-xl">
            <Link
                :href="route('admin.users.index')"
                class="mb-3 inline-flex items-center gap-1.5 text-sm font-medium text-stone-500 hover:text-primary-700"
            >
                <i class="pi pi-arrow-left text-xs" /> Volver a usuarios
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
                <div>
                    <InputLabel :value="isEditing ? 'Nueva contraseña (opcional)' : 'Contraseña'" />
                    <TextInput v-model="form.password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                    <InputError :message="form.errors.password" class="mt-1" />
                </div>
                <div>
                    <InputLabel value="Confirmar contraseña" />
                    <TextInput v-model="form.password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                </div>
                <div>
                    <InputLabel value="Rol" />
                    <select v-model="form.role" class="mt-1 block w-full rounded-md border-stone-300 text-sm shadow-sm">
                        <option v-for="role in roles" :key="role" :value="role">{{ role }}</option>
                    </select>
                    <InputError :message="form.errors.role" class="mt-1" />
                </div>
                <div class="flex gap-3">
                    <Button type="submit" label="Guardar" :loading="form.processing" />
                    <Link :href="route('admin.users.index')" class="inline-flex items-center text-sm text-stone-500 hover:text-stone-700">
                        Cancelar
                    </Link>
                </div>
            </form>
            </div>
        </div>
    </AdminLayout>
</template>
