<script setup>
import ShopLayout from '@/Layouts/ShopLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import RadioButton from 'primevue/radiobutton';
import { computed, ref } from 'vue';

const props = defineProps({
    cart: Object,
    addresses: Array,
});

const subtotal = computed(() =>
    props.cart.items.reduce((sum, item) => sum + item.quantity * Number(item.unit_price), 0),
);

const form = useForm({
    delivery_method: 'domicilio',
    address_id: props.addresses.find((a) => a.is_default)?.id ?? props.addresses[0]?.id ?? null,
    payment_method: 'transferencia',
    payment_proof: null,
});

const proofInput = ref(null);

function onProofChange(event) {
    form.payment_proof = event.target.files[0] ?? null;
}

function submit() {
    form.post(route('checkout.store'), { forceFormData: true });
}
</script>

<template>
    <ShopLayout title="Checkout">
        <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
            <h1 class="mb-6 text-xl font-bold text-stone-900">Finalizar compra</h1>

            <form @submit.prevent="submit" class="space-y-8">
                <section class="rounded-lg border border-stone-200 bg-white p-6">
                    <h2 class="mb-4 font-semibold text-stone-800">Método de entrega</h2>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2">
                            <RadioButton v-model="form.delivery_method" value="domicilio" />
                            Entrega a domicilio (a nivel nacional)
                        </label>
                        <label class="flex items-center gap-2">
                            <RadioButton v-model="form.delivery_method" value="recoger_tienda" />
                            Recoger en tienda
                        </label>
                    </div>

                    <div v-if="form.delivery_method === 'domicilio'" class="mt-4">
                        <InputLabel value="Dirección de envío" />
                        <div v-if="addresses.length === 0" class="mt-2 text-sm text-stone-500">
                            No tienes direcciones guardadas.
                            <a :href="route('shop.addresses.index')" class="text-primary-700 hover:underline">Agrega una</a>.
                        </div>
                        <div v-else class="mt-2 space-y-2">
                            <label
                                v-for="address in addresses"
                                :key="address.id"
                                class="flex items-start gap-2 rounded border border-stone-200 p-3"
                            >
                                <RadioButton v-model="form.address_id" :value="address.id" class="mt-1" />
                                <span class="text-sm">
                                    <strong>{{ address.recipient_name }}</strong> — {{ address.address_line }},
                                    {{ address.municipality }}, {{ address.department }}
                                    <span v-if="address.zone">(Zona {{ address.zone }})</span>
                                </span>
                            </label>
                        </div>
                        <InputError :message="form.errors.address_id" class="mt-2" />
                    </div>
                </section>

                <section class="rounded-lg border border-stone-200 bg-white p-6">
                    <h2 class="mb-4 font-semibold text-stone-800">Método de pago</h2>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2">
                            <RadioButton v-model="form.payment_method" value="transferencia" />
                            Transferencia / depósito bancario
                        </label>
                        <label class="flex items-center gap-2">
                            <RadioButton v-model="form.payment_method" value="contra_entrega" />
                            Pago contra entrega
                        </label>
                        <label class="flex items-center gap-2">
                            <RadioButton v-model="form.payment_method" value="tarjeta_online" />
                            Tarjeta en línea
                        </label>
                    </div>

                    <div v-if="form.payment_method === 'transferencia'" class="mt-4">
                        <InputLabel value="Comprobante de pago" />
                        <input
                            ref="proofInput"
                            type="file"
                            accept="image/*,.pdf"
                            class="mt-1 block w-full text-sm"
                            @change="onProofChange"
                        />
                        <InputError :message="form.errors.payment_proof" class="mt-2" />
                    </div>
                </section>

                <section class="rounded-lg border border-stone-200 bg-white p-6">
                    <div class="flex justify-between text-lg font-bold text-stone-900">
                        <span>Total</span>
                        <span>Q {{ subtotal.toFixed(2) }}</span>
                    </div>
                    <p class="mt-1 text-xs text-stone-500">Envío incluido.</p>
                </section>

                <InputError :message="form.errors.cart" />

                <Button type="submit" label="Confirmar pedido" size="large" class="w-full" :disabled="form.processing" />
            </form>
        </div>
    </ShopLayout>
</template>
