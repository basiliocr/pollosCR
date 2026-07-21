<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({ usuarios: Array });

const eliminar = (usuario) => {
    if (confirm(`Eliminar al usuario "${usuario.name}"?`)) {
        router.delete(route('usuarios.destroy', usuario.id));
    }
};
</script>

<template>
    <Head title="Usuarios" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Usuarios</h2>
                <Link :href="route('usuarios.create')" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">+ Crear usuario</Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Sucursal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Roles</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="usuario in usuarios" :key="usuario.id">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">{{ usuario.name }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ usuario.email }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ usuario.sucursal }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <span v-for="rol in usuario.roles" :key="rol" class="mr-1 rounded-full bg-indigo-100 px-2 py-1 text-xs text-indigo-800">{{ rol }}</span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    <Link :href="route('usuarios.edit', usuario.id)" class="text-indigo-600 hover:text-indigo-900">Editar</Link>
                                    <button @click="eliminar(usuario)" class="ml-4 text-red-600 hover:text-red-900">Eliminar</button>
                                </td>
                            </tr>
                            <tr v-if="usuarios.length === 0">
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No hay usuarios registrados.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>