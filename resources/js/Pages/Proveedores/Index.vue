<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({
    proveedores: Array,
});

const eliminar = (proveedor) => {
    if (confirm(`¿Eliminar el proveedor "${proveedor.nombre}"?`)) {
        router.delete(route('proveedores.destroy', proveedor.id));
    }
};
</script>

<template>
    <Head title="Proveedores" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Proveedores
                </h2>
                <Link
                    :href="route('proveedores.create')"
                    class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                >
                    + Crear proveedor
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Teléfono</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Condición de pago</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="proveedor in proveedores" :key="proveedor.id">
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">{{ proveedor.nombre }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ proveedor.telefono || '—' }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm capitalize text-gray-500">{{ proveedor.condicion_pago }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    <Link
                                        :href="route('proveedores.edit', proveedor.id)"
                                        class="text-indigo-600 hover:text-indigo-900"
                                    >
                                        Editar
                                    </Link>
                                    <button
                                        @click="eliminar(proveedor)"
                                        class="ml-4 text-red-600 hover:text-red-900"
                                    >
                                        Eliminar
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="proveedores.length === 0">
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No hay proveedores registrados todavía.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>