<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    productos: Array,
    combos: Array,
    metodos_pago: Array,
    clientes: Array,
});

const carrito = ref([]);
const mostrarCobro = ref(false);
const clienteId = ref('');
const pagos = ref([]);
const errorCobro = ref('');

const agregarProducto = (producto) => {
    const e = carrito.value.find(i => i.tipo === 'producto' && i.id === producto.id);
    if (e) e.cantidad++;
    else carrito.value.push({ tipo: 'producto', id: producto.id, nombre: producto.nombre, precio: Number(producto.precio), cantidad: 1 });
};

const agregarCombo = (combo) => {
    const e = carrito.value.find(i => i.tipo === 'combo' && i.id === combo.id);
    if (e) e.cantidad++;
    else carrito.value.push({ tipo: 'combo', id: combo.id, nombre: combo.nombre, precio: Number(combo.precio), cantidad: 1 });
};

const quitarItem = (index) => carrito.value.splice(index, 1);
const cambiarCantidad = (item, delta) => {
    item.cantidad += delta;
    if (item.cantidad < 1) item.cantidad = 1;
};

const total = computed(() => carrito.value.reduce((s, i) => s + i.precio * i.cantidad, 0));
const totalPagado = computed(() => pagos.value.reduce((s, p) => s + Number(p.monto || 0), 0));
const cambio = computed(() => totalPagado.value - total.value);

const abrirCobro = () => {
    errorCobro.value = '';
    // Empieza con una línea de pago en efectivo por el total
    const efectivo = props.metodos_pago.find(m => m.nombre.toLowerCase() === 'efectivo');
    pagos.value = [{ metodo_pago_id: efectivo ? efectivo.id : props.metodos_pago[0]?.id, monto: total.value }];
    mostrarCobro.value = true;
};

const agregarPago = () => {
    pagos.value.push({ metodo_pago_id: props.metodos_pago[0]?.id, monto: 0 });
};

const quitarPago = (index) => pagos.value.splice(index, 1);

const confirmarVenta = () => {
    errorCobro.value = '';
    if (totalPagado.value < total.value) {
        errorCobro.value = 'El monto pagado no cubre el total.';
        return;
    }
    router.post(route('ventas.store'), {
        cliente_id: clienteId.value || null,
        items: carrito.value,
        pagos: pagos.value,
    });
};
</script>

<template>
    <Head title="Nueva venta" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Nueva venta</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

                    <!-- Catálogo -->
                    <div class="lg:col-span-2">
                        <div class="rounded-lg bg-white p-4 shadow-sm">
                            <h3 class="mb-3 text-sm font-semibold text-gray-700">Productos</h3>
                            <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                                <button v-for="p in productos" :key="p.id" @click="agregarProducto(p)"
                                    class="rounded-md border border-gray-200 p-3 text-left hover:border-indigo-400 hover:bg-indigo-50">
                                    <div class="text-sm font-medium text-gray-800">{{ p.nombre }}</div>
                                    <div class="text-xs text-gray-500">{{ p.precio }} Bs · stock: {{ p.stock }}</div>
                                </button>
                            </div>
                            <h3 v-if="combos.length" class="mb-3 mt-5 text-sm font-semibold text-gray-700">Combos</h3>
                            <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                                <button v-for="c in combos" :key="c.id" @click="agregarCombo(c)"
                                    class="rounded-md border border-amber-200 bg-amber-50 p-3 text-left hover:border-amber-400">
                                    <div class="text-sm font-medium text-gray-800">{{ c.nombre }}</div>
                                    <div class="text-xs text-gray-500">{{ c.precio }} Bs</div>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Carrito y cobro -->
                    <div class="lg:col-span-1">
                        <div class="rounded-lg bg-white p-4 shadow-sm">
                            <h3 class="mb-3 text-sm font-semibold text-gray-700">Carrito</h3>

                            <div v-if="carrito.length === 0" class="py-8 text-center text-sm text-gray-400">
                                Selecciona productos para agregarlos
                            </div>

                            <div v-for="(item, index) in carrito" :key="index" class="mb-2 flex items-center justify-between border-b pb-2">
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-800">{{ item.nombre }}</div>
                                    <div class="text-xs text-gray-500">{{ item.precio }} Bs c/u</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button @click="cambiarCantidad(item, -1)" class="h-6 w-6 rounded bg-gray-100 text-gray-600 hover:bg-gray-200">−</button>
                                    <span class="w-6 text-center text-sm">{{ item.cantidad }}</span>
                                    <button @click="cambiarCantidad(item, 1)" class="h-6 w-6 rounded bg-gray-100 text-gray-600 hover:bg-gray-200">+</button>
                                    <button @click="quitarItem(index)" class="ml-1 text-red-500 hover:text-red-700">✕</button>
                                </div>
                            </div>

                            <div class="mt-4 border-t pt-4">
                                <div class="flex justify-between text-lg font-bold text-gray-800">
                                    <span>Total:</span>
                                    <span>{{ total.toFixed(2) }} Bs</span>
                                </div>
                            </div>

                            <!-- Botón cobrar (cuando no está el panel abierto) -->
                            <button v-if="!mostrarCobro" :disabled="carrito.length === 0" @click="abrirCobro"
                                class="mt-4 w-full rounded-md bg-green-600 px-4 py-3 font-medium text-white hover:bg-green-700 disabled:bg-gray-300">
                                Cobrar
                            </button>

                            <!-- Panel de cobro -->
                            <div v-if="mostrarCobro" class="mt-4 border-t pt-4">
                                <h4 class="mb-2 text-sm font-semibold text-gray-700">Cobro</h4>

                                <label class="block text-xs text-gray-600">Cliente (opcional)</label>
                                <select v-model="clienteId" class="mb-3 mt-1 block w-full rounded-md border-gray-300 text-sm">
                                    <option value="">Sin cliente</option>
                                    <option v-for="c in clientes" :key="c.id" :value="c.id">{{ c.nombre }}</option>
                                </select>

                                <label class="block text-xs text-gray-600">Pagos</label>
                                <div v-for="(pago, i) in pagos" :key="i" class="mb-2 flex items-center gap-2">
                                    <select v-model="pago.metodo_pago_id" class="flex-1 rounded-md border-gray-300 text-sm">
                                        <option v-for="m in metodos_pago" :key="m.id" :value="m.id">{{ m.nombre }}</option>
                                    </select>
                                    <input v-model="pago.monto" type="number" step="0.01" class="w-24 rounded-md border-gray-300 text-sm" />
                                    <button v-if="pagos.length > 1" @click="quitarPago(i)" class="text-red-500">✕</button>
                                </div>
                                <button @click="agregarPago" class="mb-3 text-xs text-indigo-600 hover:underline">+ Agregar otro pago (mixto)</button>

                                <div class="flex justify-between text-sm text-gray-600">
                                    <span>Pagado:</span><span>{{ totalPagado.toFixed(2) }} Bs</span>
                                </div>
                                <div v-if="cambio >= 0" class="flex justify-between text-sm font-medium text-green-700">
                                    <span>Cambio:</span><span>{{ cambio.toFixed(2) }} Bs</span>
                                </div>

                                <div v-if="errorCobro" class="mt-2 text-sm text-red-600">{{ errorCobro }}</div>

                                <button @click="confirmarVenta"
                                    class="mt-3 w-full rounded-md bg-green-600 px-4 py-3 font-medium text-white hover:bg-green-700">
                                    Confirmar venta
                                </button>
                                <button @click="mostrarCobro = false" class="mt-2 w-full text-sm text-gray-500 hover:underline">
                                    Cancelar
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>