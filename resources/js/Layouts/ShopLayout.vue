<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import Button from 'primevue/button';
import { computed, ref, watch } from 'vue';

defineProps({
    title: { type: String, default: 'Mueblería Ramírez' },
});

const page = usePage();
const user = computed(() => page.props.auth?.user);
const cartCount = computed(() => page.props.cartCount ?? 0);
const flashSuccess = computed(() => page.props.flash?.success);
const flashWarning = computed(() => page.props.flash?.warning);

const mobileMenuOpen = ref(false);

watch(() => page.url, () => {
    mobileMenuOpen.value = false;
});

const tabs = computed(() => [
    { href: route('home'), icon: 'pi-home', label: 'Inicio', active: route().current('home') },
    { href: route('catalog.index'), icon: 'pi-th-large', label: 'Catálogo', active: route().current('catalog.*') },
    { href: route('cart.index'), icon: 'pi-shopping-cart', label: 'Carrito', active: route().current('cart.*'), badge: cartCount.value },
    {
        href: user.value ? route('shop.addresses.index') : route('login'),
        icon: 'pi-user',
        label: user.value ? 'Cuenta' : 'Ingresar',
        active: route().current('shop.addresses.*') || route().current('shop.orders.*') || route().current('login'),
    },
]);
</script>

<template>
    <Head :title="title" />

    <div class="flex min-h-screen flex-col bg-primary-50">
        <div class="bg-primary-900 py-1.5 text-center text-xs font-medium tracking-wide text-accent-300">
            ♡ Tu confianza es nuestra prioridad ♡
        </div>

        <header class="sticky top-0 z-40 border-b border-primary-100 bg-white/95 backdrop-blur">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <Link :href="route('home')" class="text-xl font-bold text-primary-800">
                    Mueblería Ramírez
                </Link>

                <nav class="hidden items-center gap-6 text-sm font-medium text-stone-600 sm:flex">
                    <Link :href="route('home')" class="hover:text-accent-600">Inicio</Link>
                    <Link :href="route('catalog.index')" class="hover:text-accent-600">Catálogo</Link>
                    <Link v-if="user" :href="route('shop.orders.index')" class="hover:text-accent-600">Mis pedidos</Link>
                    <Link v-if="user?.role_names?.includes('admin')" :href="route('admin.dashboard')" class="hover:text-accent-600">Panel admin</Link>
                </nav>

                <div class="flex items-center gap-2 sm:gap-3">
                    <Link :href="route('cart.index')" class="relative hidden sm:block">
                        <Button icon="pi pi-shopping-cart" severity="secondary" text rounded aria-label="Carrito" />
                        <span
                            v-if="cartCount > 0"
                            class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-accent-500 text-xs font-bold text-white"
                        >
                            {{ cartCount }}
                        </span>
                    </Link>

                    <Link v-if="user" :href="route('shop.addresses.index')" class="hidden sm:block">
                        <Button label="Mi cuenta" severity="secondary" outlined size="small" />
                    </Link>
                    <Link v-else :href="route('login')" class="hidden sm:block">
                        <Button label="Ingresar" size="small" />
                    </Link>

                    <Link v-if="user?.role_names?.includes('admin')" :href="route('admin.dashboard')" class="hidden text-stone-500 hover:text-accent-600 sm:block" aria-label="Panel admin">
                        <i class="pi pi-cog text-lg" />
                    </Link>

                    <button
                        type="button"
                        class="text-stone-600 sm:hidden"
                        aria-label="Menú"
                        @click="mobileMenuOpen = !mobileMenuOpen"
                    >
                        <i class="pi text-xl" :class="mobileMenuOpen ? 'pi-times' : 'pi-bars'" />
                    </button>
                </div>
            </div>

            <nav v-if="mobileMenuOpen" class="space-y-1 border-t border-primary-100 px-4 py-3 text-sm font-medium text-stone-600 sm:hidden">
                <Link v-if="user" :href="route('shop.orders.index')" class="block rounded-lg px-2 py-2 hover:bg-primary-50 hover:text-accent-600">Mis pedidos</Link>
                <Link v-if="user?.role_names?.includes('admin')" :href="route('admin.dashboard')" class="block rounded-lg px-2 py-2 hover:bg-primary-50 hover:text-accent-600">Panel admin</Link>
            </nav>
        </header>

        <div
            v-if="flashSuccess"
            class="bg-green-50 px-4 py-3 text-center text-sm font-medium text-green-700"
        >
            {{ flashSuccess }}
        </div>
        <div
            v-if="flashWarning"
            class="bg-amber-50 px-4 py-3 text-center text-sm font-medium text-amber-700"
        >
            {{ flashWarning }}
        </div>

        <main class="flex-1 pb-16 sm:pb-0">
            <slot />
        </main>

        <footer class="border-t border-primary-100 bg-primary-900 py-8 text-center text-sm text-primary-200">
            Mueblería Ramírez — Guatemala
        </footer>

        <nav
            class="fixed inset-x-0 bottom-0 z-40 flex border-t border-primary-100 bg-white/95 backdrop-blur sm:hidden"
            style="padding-bottom: env(safe-area-inset-bottom)"
            aria-label="Navegación principal"
        >
            <Link
                v-for="tab in tabs"
                :key="tab.label"
                :href="tab.href"
                class="relative flex flex-1 flex-col items-center gap-0.5 py-2 text-[11px] font-medium"
                :class="tab.active ? 'text-accent-600' : 'text-stone-500'"
            >
                <i class="pi text-lg" :class="tab.icon" />
                {{ tab.label }}
                <span
                    v-if="tab.badge"
                    class="absolute right-1/4 top-1 flex h-4 w-4 items-center justify-center rounded-full bg-accent-500 text-[9px] font-bold text-white"
                >
                    {{ tab.badge }}
                </span>
            </Link>
        </nav>
    </div>
</template>
