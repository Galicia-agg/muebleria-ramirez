<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref, useSlots, watch } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

defineProps({
    title: { type: String, default: 'Panel administrativo' },
});

const slots = useSlots();
const page = usePage();
const flashSuccess = computed(() => page.props.flash?.success);
const flashWarning = computed(() => page.props.flash?.warning);
const user = computed(() => page.props.auth.user);

const links = [
    { href: 'admin.dashboard', label: 'Dashboard', icon: 'pi pi-chart-line' },
    { href: 'admin.reports.index', label: 'Reportes', icon: 'pi pi-chart-bar' },
    { href: 'admin.products.index', label: 'Productos', icon: 'pi pi-box' },
    { href: 'admin.categories.index', label: 'Categorías', icon: 'pi pi-tags' },
    { href: 'admin.orders.index', label: 'Pedidos', icon: 'pi pi-shopping-bag' },
    { href: 'admin.customers.index', label: 'Clientes', icon: 'pi pi-id-card' },
    { href: 'admin.payments.index', label: 'Pagos por verificar', icon: 'pi pi-credit-card' },
    { href: 'admin.stock-entries.index', label: 'Abastecimiento', icon: 'pi pi-truck' },
    { href: 'admin.suppliers.index', label: 'Proveedores', icon: 'pi pi-building' },
    { href: 'admin.users.index', label: 'Usuarios', icon: 'pi pi-users' },
];

const sidebarOpen = ref(false);

watch(() => page.url, () => {
    sidebarOpen.value = false;
});

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <Head :title="title" />

    <div class="flex min-h-screen bg-stone-50">
        <div
            v-if="sidebarOpen"
            class="fixed inset-0 z-30 bg-stone-900/50 lg:hidden"
            @click="sidebarOpen = false"
        />

        <aside
            class="fixed inset-y-0 left-0 z-40 flex w-64 shrink-0 -translate-x-full flex-col border-r border-stone-200 bg-white transition-transform duration-200 lg:static lg:translate-x-0"
            :class="{ 'translate-x-0': sidebarOpen }"
        >
            <div class="flex shrink-0 items-center justify-between border-b border-stone-200 px-6 py-5">
                <div>
                    <Link :href="route('home')" class="text-lg font-bold text-primary-700">
                        Mueblería Ramírez
                    </Link>
                    <p class="text-xs text-stone-500">Panel administrativo</p>
                </div>
                <button type="button" class="text-stone-400 hover:text-stone-600 lg:hidden" @click="sidebarOpen = false">
                    <i class="pi pi-times text-lg" />
                </button>
            </div>

            <nav class="min-h-0 flex-1 space-y-1 overflow-y-auto p-4">
                <Link
                    v-for="link in links"
                    :key="link.href"
                    :href="route(link.href)"
                    class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition"
                    :class="route().current(link.href.replace('.index', '.*'))
                        ? 'bg-primary-50 text-primary-700'
                        : 'text-stone-600 hover:bg-stone-100'"
                >
                    <i :class="link.icon" />
                    {{ link.label }}
                </Link>
            </nav>

            <div class="shrink-0 border-t border-stone-200 p-4">
                <Link :href="route('home')" class="text-sm text-stone-500 hover:text-primary-700">
                    ← Volver a la tienda
                </Link>
            </div>
        </aside>

        <div class="min-w-0 flex-1">
            <header class="sticky top-0 z-20 border-b border-stone-200 bg-white/95 px-4 py-4 backdrop-blur sm:px-6">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex min-w-0 items-center gap-3">
                        <button type="button" class="text-stone-500 hover:text-stone-800 lg:hidden" @click="sidebarOpen = true">
                            <i class="pi pi-bars text-lg" />
                        </button>
                        <div class="min-w-0">
                            <slot name="header" />
                        </div>
                    </div>

                    <div class="flex shrink-0 items-center gap-2 sm:gap-4">
                        <div v-if="slots.actions" class="hidden sm:block">
                            <slot name="actions" />
                        </div>

                        <div v-if="slots.actions" class="hidden h-6 w-px bg-stone-200 sm:block" />

                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button type="button" class="flex items-center gap-2 text-sm font-medium text-stone-600 hover:text-stone-900">
                                    <span class="hidden sm:inline">{{ user.name }}</span>
                                    <i class="pi pi-user inline sm:hidden" />
                                    <i class="pi pi-chevron-down text-xs" />
                                </button>
                            </template>

                            <template #content>
                                <DropdownLink :href="route('profile.show')">
                                    Perfil
                                </DropdownLink>

                                <form @submit.prevent="logout">
                                    <DropdownLink as="button">
                                        Cerrar sesión
                                    </DropdownLink>
                                </form>
                            </template>
                        </Dropdown>
                    </div>
                </div>

                <div v-if="slots.actions" class="mt-3 [&>a]:block [&_button]:w-full sm:hidden">
                    <slot name="actions" />
                </div>
            </header>

            <div
                v-if="flashSuccess"
                class="bg-green-50 px-4 py-3 text-sm font-medium text-green-700 sm:px-6"
            >
                {{ flashSuccess }}
            </div>
            <div
                v-if="flashWarning"
                class="bg-amber-50 px-4 py-3 text-sm font-medium text-amber-700 sm:px-6"
            >
                {{ flashWarning }}
            </div>

            <main class="p-4 sm:p-6">
                <slot />
            </main>
        </div>
    </div>
</template>
