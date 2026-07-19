<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    metricas: Object,
    stock_bajo: Array,
    mas_vendidos: Array,
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Inicio
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">

                <!-- Tarjetas de métricas -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <div class="text-sm font-medium text-gray-500">Ventas de hoy</div>
                        <div class="mt-2 text-3xl font-bold text-gray-800">{{ metricas.total_hoy }} Bs</div>
                    </div>
                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <div class="text-sm font-medium text-gray-500">Nº de ventas hoy</div>
                        <div class="mt-2 text-3xl font-bold text-gray-800">{{ metricas.cantidad_hoy }}</div>
                    </div>
                    <div class="rounded-lg p-6 shadow-sm" :class="metricas.stock_bajo_count > 0 ? 'bg-red-50' : 'bg-white'">
                        <div class="text-sm font-medium text-gray-500">Productos con stock bajo</div>
                        <div class="mt-2 text-3xl font-bold" :class="metricas.stock_bajo_count > 0 ? 'text-red-700' : 'text-gray-800'">
                            {{ metricas.stock_bajo_count }}
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                    <!-- Stock bajo -->
                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-gray-700">Alertas de stock bajo</h3>
                            <Link :href="route('inventario.index')" class="text-xs text-indigo-600 hover:underline">Ver inventario</Link>
                        </div>
                        <div v-if="stock_bajo.length === 0" class="py-6 text-center text-sm text-gray-400">
                            Todo el stock está por encima del mínimo 👍
                        </div>
                        <ul v-else class="space-y-2">
                            <li v-for="(item, i) in stock_bajo" :key="i" class="flex items-center justify-between rounded-md bg-red-50 px-3 py-2">
                                <div>
                                    <span class="text-sm font-medium text-gray-800">{{ item.producto }}</span>
                                    <span class="text-xs text-gray-500"> · {{ item.sucursal }}</span>
                                </div>
                                <span class="text-sm font-semibold text-red-700">{{ item.stock_actual }} / {{ item.stock_minimo }}</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Más vendidos -->
                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <h3 class="mb-4 text-sm font-semibold text-gray-700">Productos más vendidos</h3>
                        <div v-if="mas_vendidos.length === 0" class="py-6 text-center text-sm text-gray-400">
                            Aún no hay ventas registradas
                        </div>
                        <ul v-else class="space-y-2">
                            <li v-for="(item, i) in mas_vendidos" :key="i" class="flex items-center justify-between border-b pb-2">
                                <div class="flex items-center gap-2">
                                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-700">{{ i + 1 }}</span>
                                    <span class="text-sm text-gray-800">{{ item.producto }}</span>
                                </div>
                                <span class="text-sm font-medium text-gray-600">{{ item.cantidad }} vendidos</span>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>