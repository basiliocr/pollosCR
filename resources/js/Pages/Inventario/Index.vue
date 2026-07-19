<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    inventario: Array,
});

const editando = ref(null);
const nuevoMinimo = ref(0);

const empezarEdicion = (item) => {
    editando.value = item.id;
    nuevoMinimo.value = item.stock_minimo;
};

const guardarMinimo = (item) => {
    router.put(route('inventario.minimo', item.id), {
        stock_minimo: nuevoMinimo.value,
    }, {
        onSuccess: () => { editando.value = null; },
    });
};
</script>

<template>
    <Head title="Inventario" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Inventario
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Código</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Producto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Sucursal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Stock actual</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Stock mínimo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="item in inventario" :key="item.id" :class="item.bajo_minimo ? 'bg-red-50' : ''">
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">{{ item.codigo }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">{{ item.producto }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ item.sucursal }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium" :class="item.bajo_minimo ? 'text-red-700' : 'text-gray-900'">
                                    {{ item.stock_actual }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                    <div v-if="editando === item.id" class="flex items-center gap-2">
                                        <input v-model="nuevoMinimo" type="number" step="0.01" class="w-24 rounded-md border-gray-300 text-sm" />
                                        <button @click="guardarMinimo(item)" class="text-indigo-600 hover:text-indigo-900">✓</button>
                                        <button @click="editando = null" class="text-gray-400 hover:text-gray-600">✕</button>
                                    </div>
                                    <div v-else class="flex items-center gap-2">
                                        {{ item.stock_minimo }}
                                        <button @click="empezarEdicion(item)" class="text-xs text-indigo-600 hover:underline">editar</button>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    <span v-if="item.bajo_minimo" class="rounded-full bg-red-100 px-2 py-1 text-xs font-medium text-red-800">
                                        Stock bajo
                                    </span>
                                    <span v-else class="rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-800">
                                        OK
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="inventario.length === 0">
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No hay registros de inventario todavía.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>