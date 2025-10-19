<template>
    <Head title="Create User" />
    <AdminLayout>
        <div class="mx-auto min-h-screen max-w-5xl bg-gradient-to-br from-blue-50/30 via-white to-purple-50/30 p-8">
            <!-- Header -->
            <div class="mb-8 flex items-center justify-between">
                <h1 class="text-4xl font-bold tracking-tight text-gray-800">New User</h1>
                <button
                    type="button"
                    @click="$inertia.visit(route('admin.users.index'))"
                    class="inline-flex items-center rounded-xl border-2 border-gray-300 bg-white px-6 py-3 text-sm font-semibold text-gray-700 shadow-lg transition-all duration-200 hover:scale-105 hover:border-gray-400 hover:bg-gray-50 hover:shadow-xl focus:ring-4 focus:ring-gray-200 focus:outline-none"
                >
                    ← Back to List
                </button>
            </div>

            <!-- Form -->
            <form class="flex flex-col gap-8 rounded-2xl border-2 border-indigo-200 bg-white p-8 shadow-xl" @submit.prevent="submit">
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                    <!-- Left column -->
                    <div class="flex flex-col gap-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="mb-2 block text-sm font-semibold text-gray-700"> Name </label>
                            <input
                                v-model="form.name"
                                id="name"
                                type="text"
                                required
                                class="block w-full rounded-xl border-2 border-purple-200 bg-white px-4 py-3 text-sm shadow-sm transition-all focus:border-purple-500 focus:ring-4 focus:ring-purple-100 focus:outline-none"
                                placeholder="Enter name"
                            />
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="mb-2 block text-sm font-semibold text-gray-700"> Email </label>
                            <input
                                v-model="form.email"
                                id="email"
                                type="email"
                                required
                                class="block w-full rounded-xl border-2 border-blue-200 bg-white px-4 py-3 text-sm shadow-sm transition-all focus:border-blue-500 focus:ring-4 focus:ring-blue-100 focus:outline-none"
                                placeholder="Enter email"
                            />
                        </div>
                    </div>

                    <!-- Right column -->
                    <div class="flex flex-col gap-6">
                        <!-- Address -->
                        <div>
                            <label for="address" class="mb-2 block text-sm font-semibold text-gray-700"> Address </label>
                            <input
                                v-model="form.address"
                                id="address"
                                type="text"
                                class="block w-full rounded-xl border-2 border-teal-200 bg-white px-4 py-3 text-sm shadow-sm transition-all focus:border-teal-500 focus:ring-4 focus:ring-teal-100 focus:outline-none"
                                placeholder="Enter address"
                            />
                        </div>

                        <!-- Mobile Number -->
                        <div>
                            <label for="mobile_number" class="mb-2 block text-sm font-semibold text-gray-700"> Mobile Number </label>
                            <input
                                v-model="form.mobile_number"
                                id="mobile_number"
                                type="text"
                                class="block w-full rounded-xl border-2 border-indigo-200 bg-white px-4 py-3 text-sm shadow-sm transition-all focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 focus:outline-none"
                                placeholder="09xxxxxxxxx"
                            />
                        </div>

                        <!-- Role -->
                        <div>
                            <label for="role" class="mb-2 block text-sm font-semibold text-gray-700"> Role </label>
                            <select
                                v-model="form.role"
                                id="role"
                                required
                                class="block w-full rounded-xl border-2 border-cyan-200 bg-white px-4 py-3 text-sm shadow-sm transition-all focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 focus:outline-none"
                            >
                                <option value="">Select Role</option>
                                <option value="admin">Admin</option>
                                <option value="user">User</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex justify-end border-t border-gray-200 pt-4">
                    <button
                        type="submit"
                        class="inline-flex items-center rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-8 py-4 text-base font-bold text-white shadow-lg transition-all duration-200 hover:scale-105 hover:from-emerald-700 hover:to-teal-700 hover:shadow-xl focus:ring-4 focus:ring-emerald-200 focus:outline-none"
                    >
                        💾 Save User
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<script setup>
import { notify } from '@/composables/useToast';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import axios from 'axios';
const form = useForm({
    name: '',
    email: '',
    address: '',
    mobile_number: '',
    role: '',
});

const submit = async () => {
    const route_url = route('admin.users.store');
    try {
        const response = await axios.post(route_url, form);
        if (response.data.result === true) {
            console.log('User created successfully:', response.data.data);
            notify('User created successfully!', 'success');
            form.reset();
        } else {
            console.error('Error:', response.data.message);
            alert('⚠️ ' + response.data.message);
        }
    } catch (error) {
        if (error.response && error.response.data.errors) {
            console.error('Validation errors:', error.response.data.errors);
        } else {
            console.error('Unexpected error:', error);
        }
    }
};
</script>
