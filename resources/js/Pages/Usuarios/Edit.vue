<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({ usuario: Object, roles: Array, sucursales: Array });

const form = useForm({
    name: props.usuario.name,
    email: props.usuario.email,
    password: '',
    sucursal_id: props.usuario.sucursal_id || '',
    roles: [...props.usuario.roles],
});

const submit = () => form.put(route('usuarios.update', props.usuario.id));
</script>

<template>
    <Head title="Editar usuario" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Editar usuario</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nombre</label>
                            <input v-model="form.name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
                            <div v-if="form.errors.name" class="text-sm text-red-600">{{ form.errors.name }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <input v-model="form.email" type="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
                            <div v-if="form.errors.email" class="text-sm text-red-600">{{ form.errors.email }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Contraseña (dejar vacío para no cambiar)</label>
                            <input v-model="form.password" type="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
                            <div v-if="form.errors.password" class="text-sm text-red-600">{{ form.errors.password }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Sucursal</label>
                            <select v-model="form.sucursal_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">Sin sucursal</option>
                                <option v-for="s in sucursales" :key="s.id" :value="s.id">{{ s.nombre }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Roles</label>
                            <div class="mt-2 space-y-1">
                                <label v-for="rol in roles" :key="rol" class="flex items-center gap-2">
                                    <input type="checkbox" :value="rol" v-model="form.roles" class="rounded border-gray-300" />
                                    <span class="text-sm text-gray-700">{{ rol }}</span>
                                </label>
                            </div>
                            <div v-if="form.errors.roles" class="text-sm text-red-600">{{ form.errors.roles }}</div>
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