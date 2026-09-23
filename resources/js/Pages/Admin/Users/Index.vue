<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import Button from 'primevue/button';
import { computed, ref } from 'vue';

const props = defineProps({
    users: Array,
    roles: Array,
});

const page = usePage();
const currentUserId = computed(() => page.props.auth.user.id);

const editingId = ref(null);

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: props.roles[0] ?? '',
});

function submit() {
    if (editingId.value) {
        form.transform((data) => (data.password ? data : { ...data, password: null, password_confirmation: null }))
            .put(route('admin.users.update', editingId.value), { onSuccess: () => cancelEdit() });
    } else {
        form.post(route('admin.users.store'), { onSuccess: () => form.reset() });
    }
}

function edit(user) {
    editingId.value = user.id;
    form.name = user.name;
    form.email = user.email;
    form.password = '';
    form.password_confirmation = '';
    form.role = user.roles[0]?.name ?? props.roles[0] ?? '';
}

function cancelEdit() {
    editingId.value = null;
    form.reset();
}

function destroy(user) {
    if (confirm(`¿Eliminar al usuario "${user.name}"?`)) {
        router.delete(route('admin.users.destroy', user.id));
    }
}
</script>

<template>
    <AdminLayout title="Usuarios">
        <template #header>
            <h1 class="text-lg font-semibold text-stone-900">Usuarios</h1>
        </template>

        <div class="grid gap-6 md:grid-cols-3">
            <div class="overflow-hidden rounded-lg border border-stone-200 bg-white md:col-span-2">
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-stone-200">
                    <thead class="bg-stone-50">
                        <tr class="text-left text-xs font-semibold uppercase tracking-wide text-stone-500">
                            <th class="px-6 py-3">Nombre</th>
                            <th class="px-6 py-3">Email</th>
                            <th class="px-6 py-3">Rol</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100 text-sm text-stone-700">
                        <tr v-for="user in users" :key="user.id">
                            <td class="px-6 py-3 font-medium text-stone-900">{{ user.name }}</td>
                            <td class="px-6 py-3">{{ user.email }}</td>
                            <td class="px-6 py-3 capitalize">{{ user.roles.map((r) => r.name).join(', ') || '—' }}</td>
                            <td class="px-6 py-3 text-right">
                                <button class="mr-3 font-medium text-primary-600 hover:text-primary-800" @click="edit(user)">Editar</button>
                                <button
                                    v-if="user.id !== currentUserId"
                                    class="font-medium text-red-600 hover:text-red-800"
                                    @click="destroy(user)"
                                >
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                        <tr v-if="users.length === 0">
                            <td colspan="4" class="px-6 py-10 text-center text-stone-500">No hay usuarios.</td>
                        </tr>
                    </tbody>
                </table>
              </div>
            </div>

            <div class="rounded-lg border border-stone-200 bg-white p-6">
                <h3 class="mb-4 text-sm font-semibold text-stone-900">
                    {{ editingId ? 'Editar usuario' : 'Nuevo usuario' }}
                </h3>
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <InputLabel value="Nombre" />
                        <TextInput v-model="form.name" class="mt-1 block w-full" />
                        <InputError :message="form.errors.name" class="mt-1" />
                    </div>
                    <div>
                        <InputLabel value="Email" />
                        <TextInput v-model="form.email" type="email" class="mt-1 block w-full" />
                        <InputError :message="form.errors.email" class="mt-1" />
                    </div>
                    <div>
                        <InputLabel :value="editingId ? 'Nueva contraseña (opcional)' : 'Contraseña'" />
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
                        <Button type="submit" label="Guardar" :disabled="form.processing" />
                        <button v-if="editingId" type="button" class="text-sm text-stone-500" @click="cancelEdit">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
