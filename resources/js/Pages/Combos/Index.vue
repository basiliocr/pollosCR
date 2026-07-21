<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({ combos: Array });

const eliminar = (combo) => {
    if (confirm(`Eliminar el combo "${combo.nombre}"?`)) {
        router.delete(route('combos.destroy', combo.id));
    }
};
</script>

<template>
    <Head title="Combos" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Combos</h2>
                <Link :href="route('combos.create')" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">+ Crear combo</Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Incluye</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Precio (Bs)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="combo in combos" :key="combo.id">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">{{ combo.nombre }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <span v-for="(p, i) in combo.productos" :key="i">{{ p.cantidad }}× {{ p.nombre }}<span v-if="i < combo.productos.length - 1">, </span></span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">{{ combo.precio }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    <span v-if="combo.activo" class="rounded-full bg-green-100 px-2 py-1 text-xs text-green-800">Activo</span>
                                    <span v-else class="rounded-full bg-gray-100 px-2 py-1 text-xs text-gray-600">Inactivo</span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    <button @click="eliminar(combo)" class="text-red-600 hover:text-red-900">Eliminar</button>
                                </td>
                            </tr>
                            <tr v-if="combos.length === 0">
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No hay combos registrados todavía.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>