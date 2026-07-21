<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({ platos: Array });

const form = useForm({
    producto_id: '',
    cantidad: 1,
});

const producir = () => {
    form.post(route('produccion.producir'), {
        onSuccess: () => form.reset('cantidad'),
    });
};
</script>

<template>
    <Head title="Producción" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Producción</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div class="mb-4 rounded-md bg-blue-50 p-4 text-sm text-blue-700">
                    Registra cuántos platos preparaste. El sistema descontará automáticamente los insumos del inventario según la receta.
                </div>

                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <form @submit.prevent="producir" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Plato preparado</label>
                            <select v-model="form.producto_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">Seleccione...</option>
                                <option v-for="p in platos" :key="p.id" :value="p.id">{{ p.nombre }}</option>
                            </select>
                            <div v-if="form.errors.producto_id" class="text-sm text-red-600">{{ form.errors.producto_id }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Cantidad preparada</label>
                            <input v-model="form.cantidad" type="number" min="1" class="mt-1 block w-32 rounded-md border-gray-300 shadow-sm" />
                            <div v-if="form.errors.cantidad" class="text-sm text-red-600">{{ form.errors.cantidad }}</div>
                        </div>

                        <div v-if="form.errors.produccion" class="text-sm text-red-600">{{ form.errors.produccion }}</div>

                        <div class="flex justify-end">
                            <button type="submit" :disabled="form.processing" class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">
                                Registrar producción
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>