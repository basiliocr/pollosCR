<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    compras: Array,
});
</script>

<template>
    <Head title="Compras" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Compras
                </h2>
                <Link
                    :href="route('compras.create')"
                    class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                >
                    + Registrar compra
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Proveedor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Sucursal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Usuario</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Total (Bs)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Factura</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="compra in compras" :key="compra.id">
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">{{ compra.fecha }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">{{ compra.proveedor }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ compra.sucursal }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ compra.usuario }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">{{ compra.total }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    <span v-if="compra.tiene_factura" class="rounded-full bg-green-100 px-2 py-1 text-xs text-green-800">Sí</span>
                                    <span v-else class="rounded-full bg-gray-100 px-2 py-1 text-xs text-gray-600">No</span>
                                </td>
                            </tr>
                            <tr v-if="compras.length === 0">
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No hay compras registradas todavía.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>