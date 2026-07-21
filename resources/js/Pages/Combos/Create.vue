<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({ productos: Array });

const form = useForm({
    nombre: '',
    precio: '',
    activo: true,
    items: [{ producto_id: '', cantidad: 1 }],
});

const agregarLinea = () => form.items.push({ producto_id: '', cantidad: 1 });
const quitarLinea = (i) => form.items.splice(i, 1);

const submit = () => form.post(route('combos.store'));
</script>

<template>
    <Head title="Crear combo" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Crear combo</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nombre del combo</label>
                            <input v-model="form.nombre" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
                            <div v-if="form.errors.nombre" class="text-sm text-red-600">{{ form.errors.nombre }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Precio del combo (Bs)</label>
                            <input v-model="form.precio" type="number" step="0.01" class="mt-1 block w-48 rounded-md border-gray-300 shadow-sm" />
                            <div v-if="form.errors.precio" class="text-sm text-red-600">{{ form.errors.precio }}</div>
                        </div>

                        <div>
                            <div class="mb-2 flex items-center justify-between">
                                <label class="block text-sm font-medium text-gray-700">Productos incluidos</label>
                                <button type="button" @click="agregarLinea" class="text-sm text-indigo-600 hover:underline">+ Agregar producto</button>
                            </div>
                            <div v-if="form.errors.items" class="mb-2 text-sm text-red-600">{{ form.errors.items }}</div>

                            <div v-for="(item, index) in form.items" :key="index" class="mb-2 flex items-center gap-2">
                                <select v-model="item.producto_id" class="flex-1 rounded-md border-gray-300 text-sm">
                                    <option value="">Seleccione...</option>
                                    <option v-for="prod in productos" :key="prod.id" :value="prod.id">{{ prod.nombre }}</option>
                                </select>
                                <input v-model="item.cantidad" type="number" min="1" class="w-20 rounded-md border-gray-300 text-sm" />
                                <button type="button" @click="quitarLinea(index)" v-if="form.items.length > 1" class="text-red-600">✕</button>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <input v-model="form.activo" type="checkbox" id="activo" class="rounded border-gray-300" />
                            <label for="activo" class="text-sm text-gray-700">Combo activo (disponible para venta)</label>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" :disabled="form.processing" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Guardar combo</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>