<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import { computed } from 'vue';

const props = defineProps({
    suppliers: Array,
    products: Array,
});

function today() {
    return new Date().toISOString().slice(0, 10);
}

const form = useForm({
    supplier_id: props.suppliers[0]?.id ?? '',
    reference: '',
    received_at: today(),
    notes: '',
    items: [{ product_id: '', quantity: 1, unit_cost: '' }],
});

function productById(id) {
    return props.products.find((product) => product.id === Number(id));
}

function addRow() {
    form.items.push({ product_id: '', quantity: 1, unit_cost: '' });
}

function removeRow(index) {
    if (form.items.length > 1) {
        form.items.splice(index, 1);
    }
}

const total = computed(() =>
    form.items.reduce((sum, item) => sum + (Number(item.unit_cost) || 0) * (Number(item.quantity) || 0), 0)
);

function submit() {
    form.post(route('admin.stock-entries.store'));
}
</script>

<template>
    <AdminLayout title="Registrar ingreso">
        <template #header>
            <h1 class="text-lg font-semibold text-stone-900">Registrar ingreso de mercancía</h1>
            <p class="text-sm text-stone-500">Abastecimiento de proveedores: reposición de productos existentes o nuevos.</p>
        </template>

        <div class="max-w-4xl rounded-lg border border-stone-200 bg-white p-6">
            <form @submit.prevent="submit" class="space-y-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <InputLabel value="Proveedor" />
                        <select v-model="form.supplier_id" class="mt-1 block w-full rounded-md border-stone-300 text-sm shadow-sm">
                            <option value="" disabled>Selecciona un proveedor</option>
                            <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                                {{ supplier.name }}
                            </option>
                        </select>
                        <InputError :message="form.errors.supplier_id" class="mt-1" />
                        <p v-if="suppliers.length === 0" class="mt-1 text-xs text-amber-600">
                            No hay proveedores activos. Crea uno primero en Proveedores.
                        </p>
                    </div>

                    <div>
                        <InputLabel value="Fecha de recepción" />
                        <TextInput v-model="form.received_at" type="date" class="mt-1 block w-full" />
                        <InputError :message="form.errors.received_at" class="mt-1" />
                    </div>

                    <div>
                        <InputLabel value="Referencia / factura (opcional)" />
                        <TextInput v-model="form.reference" class="mt-1 block w-full" />
                    </div>
                </div>

                <div>
                    <InputLabel value="Notas (opcional)" />
                    <textarea v-model="form.notes" rows="2" class="mt-1 block w-full rounded-md border-stone-300 text-sm shadow-sm"></textarea>
                </div>

                <div>
                    <div class="mb-2 flex items-center justify-between">
                        <InputLabel value="Productos recibidos" />
                        <button type="button" class="text-sm font-medium text-primary-600 hover:text-primary-800" @click="addRow">
                            + Agregar producto
                        </button>
                    </div>

                    <div class="space-y-3">
                        <div
                            v-for="(item, index) in form.items"
                            :key="index"
                            class="rounded-lg border border-stone-200 p-3"
                        >
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-12 sm:items-start">
                                <div class="sm:col-span-6">
                                    <select v-model="item.product_id" class="block w-full rounded-md border-stone-300 text-sm shadow-sm">
                                        <option value="" disabled>Selecciona un producto</option>
                                        <option v-for="product in products" :key="product.id" :value="product.id">
                                            {{ product.sku }} — {{ product.name }}
                                        </option>
                                    </select>
                                    <p v-if="productById(item.product_id)" class="mt-1 text-xs text-stone-500">
                                        Color: {{ productById(item.product_id).color || '—' }} ·
                                        Material: {{ productById(item.product_id).material || '—' }} ·
                                        Stock actual: {{ productById(item.product_id).stock }}
                                    </p>
                                    <InputError :message="form.errors[`items.${index}.product_id`]" class="mt-1" />
                                </div>

                                <div class="sm:col-span-2">
                                    <TextInput v-model="item.quantity" type="number" min="1" placeholder="Cantidad" class="block w-full" />
                                    <InputError :message="form.errors[`items.${index}.quantity`]" class="mt-1" />
                                </div>

                                <div class="sm:col-span-3">
                                    <TextInput v-model="item.unit_cost" type="number" step="0.01" min="0" placeholder="Costo (opc.)" class="block w-full" />
                                </div>

                                <div class="flex justify-end sm:col-span-1">
                                    <button
                                        type="button"
                                        class="text-sm font-medium text-red-600 hover:text-red-800 disabled:opacity-30"
                                        :disabled="form.items.length === 1"
                                        @click="removeRow(index)"
                                    >
                                        Quitar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <InputError :message="form.errors.items" class="mt-2" />
                </div>

                <div class="flex items-center justify-between border-t border-stone-200 pt-4">
                    <p class="text-sm text-stone-600">
                        Costo total estimado: <span class="font-semibold text-accent-700">Q {{ total.toFixed(2) }}</span>
                    </p>
                    <Button type="submit" label="Registrar ingreso" :disabled="form.processing" />
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
