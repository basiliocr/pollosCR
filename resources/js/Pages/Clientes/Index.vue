<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({ clientes: Array });

const eliminar = (cliente) => {
    if (confirm(`Eliminar al cliente "${cliente.nombre}"?`)) {
        router.delete(route('clientes.destroy', cliente.id));
    }
};
</script>

<template>
    <Head title="Clientes" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Clientes</h2>
                <Link :href="route('clientes.create')" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">+ Crear cliente</Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">NIT</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="cliente in clientes" :key="cliente.id">
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">{{ cliente.nombre }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ cliente.nit || '—' }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    <Link :href="route('clientes.edit', cliente.id)" class="text-indigo-600 hover:text-indigo-900">Editar</Link>
                                    <button @click="eliminar(cliente)" class="ml-4 text-red-600 hover:text-red-900">Eliminar</button>
                                </td>
                            </tr>
                            <tr v-if="clientes.length === 0">
                                <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">No hay clientes registrados todavía.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>