<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    proveedores: Array,
    productos: Array,
    sucursales: Array,
});

const hoy = new Date().toISOString().slice(0, 10);

const form = useForm({
    proveedor_id: '',
    sucursal_id: '',
    fecha: hoy,
    tiene_factura: false,
    items: [
        { producto_id: '', cantidad: 1, precio_unitario: 0 },
    ],
});

const agregarLinea = () => {
    form.items.push({ producto_id: '', cantidad: 1, precio_unitario: 0 });
};

const quitarLinea = (index) => {
    form.items.splice(index, 1);
};

const total = computed(() => {
    return form.items.reduce((sum, item) => {
        return sum + (Number(item.cantidad) * Number(item.precio_unitario));
    }, 0);
});

const submit = () => {
    form.post(route('compras.store'));
};
</script>

<template>
    <Head title="Registrar compra" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Registrar compra
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Datos generales -->
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Proveedor</label>
                                <select v-model="form.proveedor_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="">Seleccione...</option>
                                    <option v-for="p in proveedores" :key="p.id" :value="p.id">{{ p.nombre }}</option>
                                </select>
                                <div v-if="form.errors.proveedor_id" class="text-sm text-red-600">{{ form.errors.proveedor_id }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Sucursal</label>
                                <select v-model="form.sucursal_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="">Seleccione...</option>
                                    <option v-for="s in sucursales" :key="s.id" :value="s.id">{{ s.nombre }}</option>
                                </select>
                                <div v-if="form.errors.sucursal_id" class="text-sm text-red-600">{{ form.errors.sucursal_id }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Fecha</label>
                                <input v-model="form.fecha" type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
                                <div v-if="form.errors.fecha" class="text-sm text-red-600">{{ form.errors.fecha }}</div>
                            </div>

                            <div class="flex items-center gap-2 pt-6">
                                <input v-model="form.tiene_factura" type="checkbox" id="factura" class="rounded border-gray-300" />
                                <label for="factura" class="text-sm text-gray-700">La compra tiene factura</label>
                            </div>
                        </div>

                        <!-- Líneas de productos -->
                        <div>
                            <div class="mb-2 flex items-center justify-between">
                                <h3 class="text-sm font-semibold text-gray-700">Productos comprados</h3>
                                <button type="button" @click="agregarLinea" class="text-sm text-indigo-600 hover:underline">
                                    + Agregar producto
                                </button>
                            </div>

                            <div v-if="form.errors.items" class="mb-2 text-sm text-red-600">{{ form.errors.items }}</div>

                            <table class="min-w-full">
                                <thead>
                                    <tr class="text-left text-xs uppercase text-gray-500">
                                        <th class="pb-2">Producto</th>
                                        <th class="pb-2">Cantidad</th>
                                        <th class="pb-2">Precio unit. (Bs)</th>
                                        <th class="pb-2">Subtotal</th>
                                        <th class="pb-2"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in form.items" :key="index">
                                        <td class="py-1 pr-2">
                                            <select v-model="item.producto_id" class="block w-full rounded-md border-gray-300 text-sm">
                                                <option value="">Seleccione...</option>
                                                <option v-for="prod in productos" :key="prod.id" :value="prod.id">
                                                    {{ prod.codigo }} - {{ prod.nombre }}
                                                </option>
                                            </select>
                                        </td>
                                        <td class="py-1 pr-2">
                                            <input v-model="item.cantidad" type="number" step="0.001" min="0" class="w-24 rounded-md border-gray-300 text-sm" />
                                        </td>
                                        <td class="py-1 pr-2">
                                            <input v-model="item.precio_unitario" type="number" step="0.01" min="0" class="w-28 rounded-md border-gray-300 text-sm" />
                                        </td>
                                        <td class="py-1 pr-2 text-sm text-gray-700">
                                            {{ (Number(item.cantidad) * Number(item.precio_unitario)).toFixed(2) }}
                                        </td>
                                        <td class="py-1">
                                            <button type="button" @click="quitarLinea(index)" v-if="form.items.length > 1" class="text-red-600 hover:text-red-900">
                                                ✕
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="mt-4 text-right text-lg font-semibold text-gray-800">
                                Total: {{ total.toFixed(2) }} Bs
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" :disabled="form.processing" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                                Registrar compra
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>