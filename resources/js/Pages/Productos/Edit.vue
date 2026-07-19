<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    producto: Object,
    categorias: Array,
    unidades: Array,
});

const form = useForm({
    codigo: props.producto.codigo,
    nombre: props.producto.nombre,
    precio: props.producto.precio,
    categoria_id: props.producto.categoria_id,
    unidad_id: props.producto.unidad_id,
    requiere_receta: props.producto.requiere_receta,
});

const submit = () => {
    form.put(route('productos.update', props.producto.id));
};
</script>

<template>
    <Head title="Editar producto" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Editar producto
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Código</label>
                            <input v-model="form.codigo" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
                            <div v-if="form.errors.codigo" class="text-sm text-red-600">{{ form.errors.codigo }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nombre</label>
                            <input v-model="form.nombre" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
                            <div v-if="form.errors.nombre" class="text-sm text-red-600">{{ form.errors.nombre }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Precio (Bs)</label>
                            <input v-model="form.precio" type="number" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
                            <div v-if="form.errors.precio" class="text-sm text-red-600">{{ form.errors.precio }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Categoría</label>
                            <select v-model="form.categoria_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">Seleccione...</option>
                                <option v-for="c in categorias" :key="c.id" :value="c.id">{{ c.nombre }}</option>
                            </select>
                            <div v-if="form.errors.categoria_id" class="text-sm text-red-600">{{ form.errors.categoria_id }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Unidad</label>
                            <select v-model="form.unidad_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">Seleccione...</option>
                                <option v-for="u in unidades" :key="u.id" :value="u.id">{{ u.nombre }}</option>
                            </select>
                            <div v-if="form.errors.unidad_id" class="text-sm text-red-600">{{ form.errors.unidad_id }}</div>
                        </div>

                        <div class="flex items-center gap-2">
                            <input v-model="form.requiere_receta" type="checkbox" id="receta" class="rounded border-gray-300" />
                            <label for="receta" class="text-sm text-gray-700">Requiere receta (plato preparado)</label>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" :disabled="form.processing" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                                Actualizar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>