<template>
    <Head title="Users Table" />
    <AdminLayout>
        <div class="mx-auto min-h-screen max-w-7xl bg-gradient-to-br from-blue-50/30 via-white to-purple-50/30 p-8">
            <!-- Header -->
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <h1 class="text-4xl font-bold tracking-tight text-gray-800">Users Table</h1>

                <div class="flex w-full gap-3 sm:w-auto">
                    <!-- Search -->
                    <input
                        v-model="search"
                        @keyup.enter="fetchUsers()"
                        type="text"
                        placeholder="Search users..."
                        class="w-full rounded-xl border-2 border-blue-200 px-4 py-3 text-sm placeholder-gray-400 transition-all outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 sm:w-64"
                    />

                    <!-- New User Button -->
                    <button
                        class="inline-flex items-center rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-3 text-sm font-semibold whitespace-nowrap text-white shadow-lg transition-all duration-200 hover:scale-105 hover:from-emerald-700 hover:to-teal-700 hover:shadow-xl focus:ring-4 focus:ring-emerald-200 focus:outline-none"
                        @click="$inertia.visit(route('admin.users.create'))"
                    >
                        ✨ New User
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-hidden rounded-xl border-2 border-indigo-200 bg-white shadow-xl">
                <BaseTable :columns="columns" :data="users" :pagination="pagination" @page-change="fetchUsers">
                    <!-- Role -->
                    <template #role="{ row }">
                        <span
                            :class="[
                                'inline-flex items-center rounded-full px-3 py-1 text-xs font-bold shadow-sm',
                                row.role === 'admin'
                                    ? 'border border-purple-200 bg-gradient-to-r from-purple-100 to-pink-100 text-purple-800'
                                    : 'border border-teal-200 bg-gradient-to-r from-teal-100 to-green-100 text-teal-800',
                            ]"
                        >
                            {{ row.role }}
                        </span>
                    </template>

                    <!-- Actions -->
                    <template #actions="{ row }">
                        <div class="flex items-center space-x-2">
                            <button
                                @click="edit(row.id)"
                                class="rounded-lg bg-gradient-to-r from-blue-500 to-indigo-500 px-3 py-2 text-xs font-semibold text-white shadow-md transition-all duration-200 hover:scale-105 hover:from-blue-600 hover:to-indigo-600 hover:shadow-lg"
                            >
                                ✏️ Edit
                            </button>
                            <button
                                @click="deleteUser(row.id)"
                                class="rounded-lg bg-gradient-to-r from-red-500 to-pink-500 px-3 py-2 text-xs font-semibold text-white shadow-md transition-all duration-200 hover:scale-105 hover:from-red-600 hover:to-pink-600 hover:shadow-lg"
                            >
                                🗑️ Delete
                            </button>
                        </div>
                    </template>
                </BaseTable>
            </div>
        </div>

        <!-- ✏️ Edit Modal -->
        <div
            v-if="isEditModalOpen"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-md transition-all duration-300"
        >
            <div
                class="w-full max-w-2xl scale-100 transform rounded-3xl bg-gradient-to-br from-white via-indigo-50 to-blue-50 p-8 shadow-2xl transition-all duration-300 hover:scale-[1.01]"
            >
                <div class="mb-6 flex items-center justify-between border-b border-indigo-100 pb-4">
                    <h2 class="flex items-center gap-2 text-3xl font-bold tracking-tight text-gray-800">✏️ Edit User</h2>
                    <button
                        @click="closeEditModal"
                        class="rounded-full p-2 text-gray-500 transition-all duration-200 hover:bg-gray-200 hover:text-gray-700"
                        title="Close"
                    >
                        ✖
                    </button>
                </div>

                <form @submit.prevent="updateUser" class="space-y-5">
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-gray-700">Name</label>
                        <input
                            type="text"
                            v-model="editUser.name"
                            class="w-full rounded-xl border-2 border-gray-200 px-4 py-3 text-gray-700 transition-all focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                        />
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-semibold text-gray-700">Email</label>
                        <input
                            type="email"
                            v-model="editUser.email"
                            class="w-full rounded-xl border-2 border-gray-200 px-4 py-3 text-gray-700 transition-all focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                        />
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-semibold text-gray-700">Address</label>
                        <input
                            type="text"
                            v-model="editUser.address"
                            class="w-full rounded-xl border-2 border-gray-200 px-4 py-3 text-gray-700 transition-all focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                        />
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-semibold text-gray-700">Mobile Number</label>
                        <input
                            type="text"
                            v-model="editUser.mobile_number"
                            class="w-full rounded-xl border-2 border-gray-200 px-4 py-3 text-gray-700 transition-all focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                        />
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-semibold text-gray-700">Role</label>
                        <select
                            v-model="editUser.role"
                            class="w-full rounded-xl border-2 border-gray-200 px-4 py-3 text-gray-700 transition-all focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                        >
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                        </select>
                    </div>

                    <div class="flex justify-end gap-4 border-t border-indigo-100 pt-4">
                        <button
                            type="button"
                            @click="closeEditModal"
                            class="rounded-xl border-2 border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-600 transition-all duration-200 hover:bg-gray-100 hover:text-gray-800"
                        >
                            Cancel
                        </button>

                        <button
                            type="submit"
                            class="rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-lg transition-all duration-200 hover:scale-105 hover:from-blue-700 hover:to-indigo-700 hover:shadow-xl"
                        >
                            💾 Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import BaseTable from '@/components/BaseTable.vue';
import { notify } from '@/composables/useToast';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, ref, watch } from 'vue';

const users = ref([]);
const pagination = ref({});
const search = ref('');
const isEditModalOpen = ref(false);
const editUser = ref({});

const columns = [
    { key: 'name', label: 'Name' },
    { key: 'email', label: 'Email' },
    { key: 'address', label: 'Address' },
    { key: 'mobile_number', label: 'Mobile Number' },
    { key: 'role', label: 'Role' },
    { key: 'actions', label: 'Actions' },
];

const fetchUsers = async (page = 1) => {
    try {
        const route_url = route('admin.users.list');
        const response = await axios.get(route_url, { params: { search: search.value, page } });

        if (response.data.result === true) {
            users.value = response.data.data;
            pagination.value = response.data.pagination;
        } else {
            notify('Failed to fetch users.', 'error');
        }
    } catch (error) {
        console.error('Failed to fetch users', error);
    }
};

const edit = async (id) => {
    const route_url = route('admin.users.edit', { id });
    try {
        const response = await axios.get(route_url);
        if (response.data.result === true) {
            editUser.value = response.data.data;
            isEditModalOpen.value = true;
        }
    } catch (error) {
        console.error('Failed to load user', error);
    }
};

const updateUser = async () => {
    const route_url = route('admin.users.update');
    try {
        const response = await axios.post(route_url, editUser.value);
        if (response.data.result === true) {
            closeEditModal();
            fetchUsers();
            notify('User updated successfully!', 'success');
        } else {
            notify('Failed to update user.', 'error');
        }
    } catch (error) {
        console.error('Update failed', error);
    }
};

const deleteUser = async (id) => {
    if (!confirm('Are you sure you want to delete this user?')) return;
    const route_url = route('admin.users.destroy', { id });
    try {
        const response = await axios.post(route_url);
        if (response.data.result === true) {
            fetchUsers();
            notify('User deleted successfully!', 'success');
        } else {
            notify('Failed to delete user.', 'error');
        }
    } catch (error) {
        console.error('Failed to delete user', error);
    }
};

const closeEditModal = () => {
    isEditModalOpen.value = false;
    editUser.value = {};
};

onMounted(fetchUsers);
watch(search, () => fetchUsers(1));
</script>
