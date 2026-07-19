<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({
    productos: Array,
});

const eliminar = (producto) => {
    if (confirm(`¿Eliminar el producto "${producto.nombre}"?`)) {
        router.delete(route('productos.destroy', producto.id));
    }
};
</script>

<template>
    <Head title="Productos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Productos
                </h2>
                <Link
                    :href="route('productos.create')"
                    class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                >
                    + Crear producto
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Código</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Categoría</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Precio (Bs)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="producto in productos" :key="producto.id">
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">{{ producto.codigo }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">{{ producto.nombre }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ producto.categoria.nombre }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">{{ producto.precio }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    <Link
                                        :href="route('productos.edit', producto.id)"
                                        class="text-indigo-600 hover:text-indigo-900"
                                    >
                                        Editar
                                    </Link>
                                    <button
                                        @click="eliminar(producto)"
                                        class="ml-4 text-red-600 hover:text-red-900"
                                    >
                                        Eliminar
                                    </button>
                                </td>
                                
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>