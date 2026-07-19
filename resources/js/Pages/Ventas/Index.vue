<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({
    ventas: Array,
});

const cancelar = (venta) => {
    if (confirm('¿Cancelar esta venta? El stock se devolverá al inventario.')) {
        router.put(route('ventas.cancelar', venta.id));
    }
};
</script>

<template>
    <Head title="Ventas" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Ventas
                </h2>
                <Link
                    :href="route('ventas.create')"
                    class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700"
                >
                    + Nueva venta
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
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Cliente</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Usuario</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Sucursal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Total (Bs)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="venta in ventas" :key="venta.id">
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">{{ venta.fecha }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ venta.cliente }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ venta.usuario }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ venta.sucursal }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">{{ venta.total }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                
                                    <span v-if="venta.estado === 'completada'" class="rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-800">
                                        Completada
                                    </span>
                                    <span v-else class="rounded-full bg-red-100 px-2 py-1 text-xs font-medium text-red-800">
                                        Cancelada
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    <button
                                        v-if="venta.estado === 'completada'"
                                        @click="cancelar(venta)"
                                        class="text-red-600 hover:text-red-900"
                                    >
                                        Cancelar
                                    </button>
                                    <span v-else class="text-gray-400">—</span>
                                </td>
                            </tr>
                            <tr v-if="ventas.length === 0">
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No hay ventas registradas todavía.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>