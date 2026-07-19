<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    nombre: '',
    telefono: '',
    condicion_pago: '',
});

const submit = () => {
    form.post(route('proveedores.store'));
};
</script>

<template>
    <Head title="Crear proveedor" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Crear proveedor
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nombre</label>
                            <input v-model="form.nombre" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
                            <div v-if="form.errors.nombre" class="text-sm text-red-600">{{ form.errors.nombre }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                            <input v-model="form.telefono" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
                            <div v-if="form.errors.telefono" class="text-sm text-red-600">{{ form.errors.telefono }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Condición de pago</label>
                            <select v-model="form.condicion_pago" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">Seleccione...</option>
                                <option value="contado">Contado</option>
                                <option value="credito">Crédito</option>
                            </select>
                            <div v-if="form.errors.condicion_pago" class="text-sm text-red-600">{{ form.errors.condicion_pago }}</div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" :disabled="form.processing" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                                Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>