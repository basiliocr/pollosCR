<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({ cliente: Object });

const form = useForm({ nombre: props.cliente.nombre, nit: props.cliente.nit });

const submit = () => form.put(route('clientes.update', props.cliente.id));
</script>

<template>
    <Head title="Editar cliente" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Editar cliente</h2>
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
                            <label class="block text-sm font-medium text-gray-700">NIT (opcional)</label>
                            <input v-model="form.nit" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
                            <div v-if="form.errors.nit" class="text-sm text-red-600">{{ form.errors.nit }}</div>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" :disabled="form.processing" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Actualizar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>