<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({ productos: Array });
</script>

<template>
    <Head title="Recetas" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Recetas</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="mb-4 rounded-md bg-blue-50 p-4 text-sm text-blue-700">
                    Aquí defines de qué insumos se compone cada plato preparado. Solo aparecen los productos marcados como "requiere receta".
                </div>
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Plato</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Insumos</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="producto in productos" :key="producto.id">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">{{ producto.nombre }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <span v-if="producto.insumos.length === 0" class="text-gray-400">Sin receta definida</span>
                                    <span v-for="(ins, i) in producto.insumos" :key="i">{{ ins.cantidad }} {{ ins.unidad }} {{ ins.insumo }}<span v-if="i < producto.insumos.length - 1">, </span></span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    <Link :href="route('recetas.edit', producto.id)" class="text-indigo-600 hover:text-indigo-900">Editar receta</Link>
                                </td>
                            </tr>
                            <tr v-if="productos.length === 0">
                                <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">No hay productos que requieran receta. Marca "requiere receta" en un producto para que aparezca aquí.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>