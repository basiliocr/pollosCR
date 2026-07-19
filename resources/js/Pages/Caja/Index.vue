<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    caja_abierta: Object,
    resumen: Object,
    historial: Array,
});

const formAbrir = useForm({
    monto_apertura: '',
});

const abrirCaja = () => {
    formAbrir.post(route('caja.abrir'), {
        onSuccess: () => formAbrir.reset(),
    });
};

const formCerrar = useForm({
    monto_cierre: '',
});

// Diferencia entre lo contado y lo esperado
const diferencia = computed(() => {
    if (formCerrar.monto_cierre === '' || !props.resumen) return null;
    return Number(formCerrar.monto_cierre) - Number(props.resumen.efectivo_esperado);
});

const cerrarCaja = () => {
    formCerrar.put(route('caja.cerrar', props.caja_abierta.id));
};
</script>

<template>
    <Head title="Caja" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Caja</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-4xl space-y-6 sm:px-6 lg:px-8">

                <!-- Si NO hay caja abierta: formulario de apertura -->
                <div v-if="!caja_abierta" class="rounded-lg bg-white p-6 shadow-sm">
                    <h3 class="mb-4 text-lg font-semibold text-gray-800">Abrir caja</h3>
                    <p class="mb-4 text-sm text-gray-500">No hay ninguna caja abierta. Ingresa el monto inicial en efectivo para empezar el turno.</p>
                    <div class="flex items-end gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Monto de apertura (Bs)</label>
                            <input v-model="formAbrir.monto_apertura" type="number" step="0.01" class="mt-1 w-48 rounded-md border-gray-300 shadow-sm" />
                            <div v-if="formAbrir.errors.monto_apertura" class="text-sm text-red-600">{{ formAbrir.errors.monto_apertura }}</div>
                            <div v-if="formAbrir.errors.caja" class="text-sm text-red-600">{{ formAbrir.errors.caja }}</div>
                        </div>
                        <button @click="abrirCaja" :disabled="formAbrir.processing" class="rounded-md bg-green-600 px-4 py-2 font-medium text-white hover:bg-green-700">
                            Abrir caja
                        </button>
                    </div>
                </div>

                <!-- Si HAY caja abierta: resumen del turno + cierre -->
                <div v-else class="space-y-6">
                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-800">Caja abierta</span>
                                <p class="mt-2 text-sm text-gray-500">Abierta por {{ caja_abierta.usuario }} el {{ caja_abierta.abierta_en }}</p>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-gray-500">Monto de apertura</div>
                                <div class="text-2xl font-bold text-gray-800">{{ caja_abierta.monto_apertura }} Bs</div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div class="rounded-lg bg-white p-6 shadow-sm">
                            <div class="text-sm text-gray-500">Ventas del turno</div>
                            <div class="mt-1 text-2xl font-bold text-gray-800">{{ resumen.total_ventas }} Bs</div>
                            <div class="text-xs text-gray-400">{{ resumen.cantidad_ventas }} ventas</div>
                        </div>
                        <div class="rounded-lg bg-white p-6 shadow-sm">
                            <div class="text-sm text-gray-500">Efectivo esperado</div>
                            <div class="mt-1 text-2xl font-bold text-indigo-700">{{ resumen.efectivo_esperado }} Bs</div>
                            <div class="text-xs text-gray-400">apertura + ventas en efectivo</div>
                        </div>
                        <div class="rounded-lg bg-white p-6 shadow-sm">
                            <div class="mb-1 text-sm text-gray-500">Por método de pago</div>
                            <ul class="text-sm text-gray-700">
                                <li v-for="(p, i) in resumen.pagos_por_metodo" :key="i" class="flex justify-between">
                                    <span>{{ p.metodo }}</span><span class="font-medium">{{ p.total }} Bs</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Panel de cierre -->
                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <h3 class="mb-4 text-lg font-semibold text-gray-800">Cerrar caja</h3>
                        <p class="mb-4 text-sm text-gray-500">
                            Cuenta el efectivo real en la caja e ingrésalo para cuadrar con lo esperado.
                        </p>

                        <div class="flex items-end gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Efectivo contado (Bs)</label>
                                <input v-model="formCerrar.monto_cierre" type="number" step="0.01" class="mt-1 w-48 rounded-md border-gray-300 shadow-sm" />
                                <div v-if="formCerrar.errors.monto_cierre" class="text-sm text-red-600">{{ formCerrar.errors.monto_cierre }}</div>
                                <div v-if="formCerrar.errors.caja" class="text-sm text-red-600">{{ formCerrar.errors.caja }}</div>
                            </div>
                            <button @click="cerrarCaja" :disabled="formCerrar.processing || formCerrar.monto_cierre === ''" class="rounded-md bg-red-600 px-4 py-2 font-medium text-white hover:bg-red-700 disabled:bg-gray-300">
                                Cerrar caja
                            </button>
                        </div>

                        <!-- Diferencia en vivo -->
                        <div v-if="diferencia !== null" class="mt-4 rounded-md p-3" :class="Math.abs(diferencia) < 0.01 ? 'bg-green-50' : 'bg-yellow-50'">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Efectivo esperado:</span>
                                <span class="font-medium">{{ resumen.efectivo_esperado }} Bs</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Efectivo contado:</span>
                                <span class="font-medium">{{ Number(formCerrar.monto_cierre).toFixed(2) }} Bs</span>
                            </div>
                            <div class="mt-1 flex justify-between border-t pt-1 text-sm font-semibold">
                                <span>Diferencia:</span>
                                <span :class="Math.abs(diferencia) < 0.01 ? 'text-green-700' : 'text-yellow-700'">
                                    {{ diferencia > 0 ? '+' : '' }}{{ diferencia.toFixed(2) }} Bs
                                    <span v-if="Math.abs(diferencia) < 0.01"> ✓ Cuadra</span>
                                    <span v-else-if="diferencia > 0"> (sobra)</span>
                                    <span v-else> (falta)</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Historial de cajas cerradas -->
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <h3 class="mb-4 text-sm font-semibold text-gray-700">Historial de cajas</h3>
                    <div v-if="historial.length === 0" class="py-4 text-center text-sm text-gray-400">
                        No hay cajas cerradas todavía.
                    </div>
                    <table v-else class="min-w-full text-sm">
                        <thead class="text-left text-xs uppercase text-gray-500">
                            <tr>
                                <th class="pb-2">Usuario</th>
                                <th class="pb-2">Apertura</th>
                                <th class="pb-2">Cierre</th>
                                <th class="pb-2">Abierta</th>
                                <th class="pb-2">Cerrada</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="c in historial" :key="c.id">
                                <td class="py-2 text-gray-700">{{ c.usuario }}</td>
                                <td class="py-2 text-gray-700">{{ c.apertura }} Bs</td>
                                <td class="py-2 text-gray-700">{{ c.cierre }} Bs</td>
                                <td class="py-2 text-gray-500">{{ c.abierta_en }}</td>
                                <td class="py-2 text-gray-500">{{ c.cerrada_en }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>