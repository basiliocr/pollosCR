<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({ producto: Object, insumos: Array, unidades: Array });

const form = useForm({
    items: props.producto.items.length > 0
        ? props.producto.items.map(i => ({ ...i }))
        : [{ insumo_id: '', cantidad: 1, unidad_id: '' }],
});

const agregarLinea = () => form.items.push({ insumo_id: '', cantidad: 1, unidad_id: '' });
const quitarLinea = (i) => form.items.splice(i, 1);

const submit = () => form.put(route('recetas.update', props.producto.id));
</script>

<template>
    <Head title="Editar receta" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Receta de: {{ producto.nombre }}</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="space-y-4">
                        <div class="mb-2 flex items-center justify-between">
                            <label class="block text-sm font-medium text-gray-700">Insumos que componen este plato</label>
                            <button type="button" @click="agregarLinea" class="text-sm text-indigo-600 hover:underline">+ Agregar insumo</button>
                        </div>

                        <div v-for="(item, index) in form.items" :key="index" class="flex items-center gap-2">
                            <select v-model="item.insumo_id" class="flex-1 rounded-md border-gray-300 text-sm">
                                <option value="">Insumo...</option>
                                <option v-for="ins in insumos" :key="ins.id" :value="ins.id">{{ ins.nombre }}</option>
                            </select>
                            <input v-model="item.cantidad" type="number" step="0.001" min="0" class="w-24 rounded-md border-gray-300 text-sm" placeholder="Cant." />
                            <select v-model="item.unidad_id" class="w-28 rounded-md border-gray-300 text-sm">
                                <option value="">Unidad...</option>
                                <option v-for="u in unidades" :key="u.id" :value="u.id">{{ u.abreviatura }}</option>
                            </select>
                            <button type="button" @click="quitarLinea(index)" v-if="form.items.length > 1" class="text-red-600">✕</button>
                        </div>

                        <div v-if="form.errors.items" class="text-sm text-red-600">{{ form.errors.items }}</div>

                        <div class="flex justify-end pt-2">
                            <button type="submit" :disabled="form.processing" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Guardar receta</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>