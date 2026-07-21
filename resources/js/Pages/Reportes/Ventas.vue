<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    filtros: Object,
    metricas: Object,
    mas_vendidos: Array,
    ventas: Array,
});

const desde = ref(props.filtros.desde);
const hasta = ref(props.filtros.hasta);

const aplicarFiltro = () => {
    router.get(route('reportes.ventas'), { desde: desde.value, hasta: hasta.value }, { preserveState: true });
};
</script>

<template>
    <Head title="Reporte de ventas" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Reporte de ventas</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">

                <div class="rounded-lg bg-white p-4 shadow-sm">
                    <div class="flex flex-wrap items-end gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Desde</label>
                            <input v-model="desde" type="date" class="mt-1 rounded-md border-gray-300 shadow-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Hasta</label>
                            <input v-model="hasta" type="date" class="mt-1 rounded-md border-gray-300 shadow-sm" />
                        </div>
                        <button @click="aplicarFiltro" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Aplicar</button>
                        <a :href="route('reportes.ventas.pdf', { desde, hasta })" target="_blank" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">Descargar PDF</a>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <div class="text-sm text-gray-500">Total vendido</div>
                        <div class="mt-1 text-3xl font-bold text-gray-800">{{ metricas.total_vendido }} Bs</div>
                    </div>
                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <div class="text-sm text-gray-500">N de ventas</div>
                        <div class="mt-1 text-3xl font-bold text-gray-800">{{ metricas.cantidad_ventas }}</div>
                    </div>
                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <div class="text-sm text-gray-500">Ticket promedio</div>
                        <div class="mt-1 text-3xl font-bold text-gray-800">{{ metricas.ticket_promedio }} Bs</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <h3 class="mb-4 text-sm font-semibold text-gray-700">Productos mas vendidos</h3>
                        <div v-if="mas_vendidos.length === 0" class="py-6 text-center text-sm text-gray-400">Sin datos en este rango</div>
                        <table v-else class="min-w-full text-sm">
                            <thead class="text-left text-xs uppercase text-gray-500">
                                <tr>
                                    <th class="pb-2">Producto</th>
                                    <th class="pb-2 text-right">Cantidad</th>
                                    <th class="pb-2 text-right">Monto</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="(p, i) in mas_vendidos" :key="i">
                                    <td class="py-2 text-gray-700">{{ p.producto }}</td>
                                    <td class="py-2 text-right text-gray-700">{{ p.cantidad }}</td>
                                    <td class="py-2 text-right font-medium text-gray-900">{{ p.monto }} Bs</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <h3 class="mb-4 text-sm font-semibold text-gray-700">Detalle de ventas</h3>
                        <div v-if="ventas.length === 0" class="py-6 text-center text-sm text-gray-400">Sin ventas en este rango</div>
                        <table v-else class="min-w-full text-sm">
                            <thead class="text-left text-xs uppercase text-gray-500">
                                <tr>
                                    <th class="pb-2">Fecha</th>
                                    <th class="pb-2">Usuario</th>
                                    <th class="pb-2 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="(v, i) in ventas" :key="i">
                                    <td class="py-2 text-gray-700">{{ v.fecha }}</td>
                                    <td class="py-2 text-gray-500">{{ v.usuario }}</td>
                                    <td class="py-2 text-right font-medium text-gray-900">{{ v.total }} Bs</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
