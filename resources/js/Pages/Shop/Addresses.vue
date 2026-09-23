<script setup>
import ShopLayout from '@/Layouts/ShopLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue';
import { router, useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import { ref } from 'vue';

const props = defineProps({
    addresses: Array,
});

const editingId = ref(null);

const form = useForm({
    recipient_name: '',
    phone: '',
    department: '',
    municipality: '',
    zone: '',
    address_line: '',
    reference: '',
    is_default: false,
});

function submit() {
    if (editingId.value) {
        form.put(route('shop.addresses.update', editingId.value), { onSuccess: () => cancelEdit() });
    } else {
        form.post(route('shop.addresses.store'), { onSuccess: () => form.reset() });
    }
}

function edit(address) {
    editingId.value = address.id;
    Object.assign(form, {
        recipient_name: address.recipient_name,
        phone: address.phone,
        department: address.department,
        municipality: address.municipality,
        zone: address.zone ?? '',
        address_line: address.address_line,
        reference: address.reference ?? '',
        is_default: address.is_default,
    });
}

function cancelEdit() {
    editingId.value = null;
    form.reset();
}

function destroy(address) {
    if (confirm('¿Eliminar esta dirección?')) {
        router.delete(route('shop.addresses.destroy', address.id));
    }
}
</script>

<template>
    <ShopLayout title="Mis direcciones">
        <div class="mx-auto grid max-w-4xl gap-6 px-4 py-10 sm:px-6 lg:px-8 md:grid-cols-2">
            <div class="space-y-4">
                <h1 class="text-xl font-bold text-stone-900">Mis direcciones</h1>
                <div
                    v-for="address in addresses"
                    :key="address.id"
                    class="rounded-lg border border-stone-200 bg-white p-4 text-sm"
                >
                    <p class="font-medium text-stone-800">
                        {{ address.recipient_name }}
                        <span v-if="address.is_default" class="ml-2 rounded-full bg-primary-50 px-2 py-0.5 text-xs text-primary-700">Predeterminada</span>
                    </p>
                    <p class="text-stone-600">{{ address.address_line }}, {{ address.municipality }}, {{ address.department }}</p>
                    <p v-if="address.zone" class="text-stone-600">Zona {{ address.zone }}</p>
                    <p class="text-stone-500">{{ address.phone }}</p>
                    <div class="mt-2 flex gap-3">
                        <button class="text-primary-700 hover:underline" @click="edit(address)">Editar</button>
                        <button class="text-red-600 hover:underline" @click="destroy(address)">Eliminar</button>
                    </div>
                </div>
                <p v-if="addresses.length === 0" class="text-stone-500">Aún no tienes direcciones guardadas.</p>
            </div>

            <div class="rounded-lg border border-stone-200 bg-white p-6">
                <h2 class="mb-4 font-semibold text-stone-800">
                    {{ editingId ? 'Editar dirección' : 'Nueva dirección' }}
                </h2>
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <InputLabel value="Nombre de quien recibe" />
                        <TextInput v-model="form.recipient_name" class="mt-1 block w-full" />
                        <InputError :message="form.errors.recipient_name" class="mt-1" />
                    </div>
                    <div>
                        <InputLabel value="Teléfono" />
                        <TextInput v-model="form.phone" class="mt-1 block w-full" />
                        <InputError :message="form.errors.phone" class="mt-1" />
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <InputLabel value="Departamento" />
                            <TextInput v-model="form.department" class="mt-1 block w-full" />
                            <InputError :message="form.errors.department" class="mt-1" />
                        </div>
                        <div>
                            <InputLabel value="Municipio" />
                            <TextInput v-model="form.municipality" class="mt-1 block w-full" />
                            <InputError :message="form.errors.municipality" class="mt-1" />
                        </div>
                    </div>
                    <div>
                        <InputLabel value="Zona (opcional)" />
                        <TextInput v-model="form.zone" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <InputLabel value="Dirección" />
                        <TextInput v-model="form.address_line" class="mt-1 block w-full" />
                        <InputError :message="form.errors.address_line" class="mt-1" />
                    </div>
                    <div>
                        <InputLabel value="Referencia (opcional)" />
                        <TextInput v-model="form.reference" class="mt-1 block w-full" />
                    </div>
                    <div class="flex items-center gap-2">
                        <Checkbox v-model:checked="form.is_default" />
                        <InputLabel value="Usar como predeterminada" />
                    </div>
                    <div class="flex gap-3">
                        <Button type="submit" label="Guardar" :disabled="form.processing" />
                        <button v-if="editingId" type="button" class="text-sm text-stone-500" @click="cancelEdit">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </ShopLayout>
</template>
