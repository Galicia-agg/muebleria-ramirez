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
</script>

<template>
    <Head :title="title" />

    <div class="flex min-h-screen flex-col bg-primary-50">
        <div class="bg-primary-900 py-1.5 text-center text-xs font-medium tracking-wide text-accent-300">
            ♡ Tu confianza es nuestra prioridad ♡
        </div>

        <header class="border-b border-primary-100 bg-white">
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
                    <Link :href="route('cart.index')" class="relative">
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
                <Link :href="route('home')" class="block rounded-lg px-2 py-2 hover:bg-primary-50 hover:text-accent-600">Inicio</Link>
                <Link :href="route('catalog.index')" class="block rounded-lg px-2 py-2 hover:bg-primary-50 hover:text-accent-600">Catálogo</Link>
                <Link v-if="user" :href="route('shop.orders.index')" class="block rounded-lg px-2 py-2 hover:bg-primary-50 hover:text-accent-600">Mis pedidos</Link>
                <Link v-if="user" :href="route('shop.addresses.index')" class="block rounded-lg px-2 py-2 hover:bg-primary-50 hover:text-accent-600">Mi cuenta</Link>
                <Link v-if="user?.role_names?.includes('admin')" :href="route('admin.dashboard')" class="block rounded-lg px-2 py-2 hover:bg-primary-50 hover:text-accent-600">Panel admin</Link>
                <Link v-if="!user" :href="route('login')" class="block rounded-lg px-2 py-2 hover:bg-primary-50 hover:text-accent-600">Ingresar</Link>
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

        <main class="flex-1">
            <slot />
        </main>

        <footer class="border-t border-primary-100 bg-primary-900 py-8 text-center text-sm text-primary-200">
            Mueblería Ramírez — Guatemala
        </footer>
    </div>
</template>
