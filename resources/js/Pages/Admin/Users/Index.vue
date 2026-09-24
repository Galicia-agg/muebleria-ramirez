<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import Button from 'primevue/button';
import { computed } from 'vue';

const props = defineProps({
    users: Array,
    roles: Array,
});

const page = usePage();
const currentUserId = computed(() => page.props.auth.user.id);

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

        <template #actions>
            <Link :href="route('admin.users.create')">
                <Button label="Nuevo usuario" icon="pi pi-plus" />
            </Link>
        </template>

        <p v-if="users.length === 0" class="rounded-lg border border-stone-200 bg-white p-10 text-center text-stone-500">
            No hay usuarios.
        </p>

        <template v-else>
            <!-- Mobile: stacked cards -->
            <ul class="space-y-3 sm:hidden">
                <li v-for="user in users" :key="user.id" class="rounded-lg border border-stone-200 bg-white p-4">
                    <p class="font-medium text-stone-900">{{ user.name }}</p>
                    <p class="truncate text-sm text-stone-500">{{ user.email }}</p>
                    <p class="mt-1 text-xs capitalize text-stone-500">{{ user.roles.map((r) => r.name).join(', ') || '—' }}</p>
                    <div class="mt-3 flex gap-4 border-t border-stone-100 pt-3 text-sm">
                        <Link :href="route('admin.users.edit', user.id)" class="font-medium text-primary-600">Editar</Link>
                        <button
                            v-if="user.id !== currentUserId"
                            class="font-medium text-red-600"
                            @click="destroy(user)"
                        >
                            Eliminar
                        </button>
                    </div>
                </li>
            </ul>

            <!-- Desktop: table -->
            <div class="hidden overflow-hidden rounded-lg border border-stone-200 bg-white sm:block">
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
                                <Link :href="route('admin.users.edit', user.id)" class="mr-3 font-medium text-primary-600 hover:text-primary-800">Editar</Link>
                                <button
                                    v-if="user.id !== currentUserId"
                                    class="font-medium text-red-600 hover:text-red-800"
                                    @click="destroy(user)"
                                >
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
              </div>
            </div>
        </template>
    </AdminLayout>
</template>
