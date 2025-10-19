<template>
    <Head title="My Profile" />
    <MainLayout>
        <div class="mx-auto mt-8 w-full max-w-6xl px-4 pb-12">
            <!-- Header with Gradient Background -->
            <div class="relative mb-8 overflow-hidden rounded-3xl bg-gradient-to-r from-green-600 via-green-500 to-emerald-500 p-8 shadow-lg">
                <div class="bg-grid-white/10 absolute inset-0"></div>
                <div class="relative flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-white">My Profile</h1>
                        <p class="mt-1 text-green-50">Manage your account settings and preferences</p>
                    </div>
                    <button
                        @click="refreshProfile"
                        class="flex items-center gap-2 rounded-xl bg-white/20 px-4 py-2 text-sm font-medium text-white backdrop-blur-sm transition hover:bg-white/30"
                    >
                        <i class="fa-solid fa-rotate-right"></i>
                        <span class="hidden sm:inline">Refresh</span>
                    </button>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Left Column - Profile Card -->
                <div class="lg:col-span-1">
                    <div class="rounded-2xl bg-white p-6 shadow-md">
                        <div class="flex flex-col items-center">
                            <!-- Profile Image -->
                            <div class="relative mb-4">
                                <img
                                    :src="photoPreview || userImage || '/default-avatar.png'"
                                    alt="Profile"
                                    class="h-32 w-32 rounded-full border-4 border-green-100 object-cover shadow-lg"
                                />
                                <input id="profilePhoto" type="file" class="hidden" @change="previewPhoto" accept="image/*" />
                                <label
                                    for="profilePhoto"
                                    class="absolute right-0 bottom-0 flex h-10 w-10 cursor-pointer items-center justify-center rounded-full bg-green-600 text-white shadow-lg transition hover:scale-110 hover:bg-green-700"
                                    title="Change photo"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path
                                            fill-rule="evenodd"
                                            d="M4 5a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V7a2 2 0 00-2-2h-1.586a1 1 0 01-.707-.293l-1.121-1.121A2 2 0 0011.172 3H8.828a2 2 0 00-1.414.586L6.293 4.707A1 1 0 015.586 5H4zm6 9a3 3 0 100-6 3 3 0 000 6z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </label>
                            </div>

                            <!-- User Info -->
                            <h2 class="text-xl font-bold text-gray-800">{{ user.name }}</h2>
                            <p class="mt-1 text-sm text-gray-500">{{ user.email }}</p>

                            <div class="mt-4 w-full rounded-lg bg-green-50 p-3">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">Role</span>
                                    <span class="font-semibold text-green-700">{{ user.role }}</span>
                                </div>
                                <div class="mt-2 flex items-center justify-between text-sm">
                                    <span class="text-gray-600">Member since</span>
                                    <span class="font-semibold text-gray-700">{{ formatDate(user.created_at) }}</span>
                                </div>
                            </div>

                            <span
                                class="mt-4 inline-flex items-center gap-2 rounded-full bg-green-100 px-4 py-1.5 text-xs font-semibold text-green-700"
                            >
                                <i class="fa-solid fa-circle-check"></i>
                                Verified Account
                            </span>

                            <!-- Upload Photo Button -->
                            <div class="mt-6 w-full">
                                <label
                                    for="visibleProfilePhoto"
                                    class="flex w-full cursor-pointer items-center justify-center gap-2 rounded-lg border-2 border-dashed border-green-300 bg-green-50 py-3 text-sm font-medium text-green-700 transition hover:border-green-400 hover:bg-green-100"
                                >
                                    <i class="fa-solid fa-upload"></i>
                                    Upload New Photo
                                    <input id="visibleProfilePhoto" type="file" class="hidden" @change="previewPhoto" accept="image/*" />
                                </label>
                                <p class="mt-2 text-center text-xs text-gray-500">JPG, PNG or GIF (max 2MB)</p>
                            </div>
                        </div>
                    </div>

                    <!-- Security Card -->
                    <div class="mt-6 rounded-2xl bg-white p-6 shadow-md">
                        <h3 class="mb-4 flex items-center gap-2 text-lg font-bold text-gray-800">
                            <i class="fa-solid fa-shield-halved text-green-600"></i>
                            Security
                        </h3>
                        <p class="mb-4 text-sm text-gray-600">Keep your account secure with a strong password.</p>
                        <button
                            @click="openOtpModal"
                            type="button"
                            class="flex w-full items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-green-600 to-green-500 px-4 py-3 font-semibold text-white shadow-md transition hover:from-green-700 hover:to-green-600"
                        >
                            <i class="fa-solid fa-key"></i>
                            Change Password
                        </button>
                    </div>
                </div>

                <!-- Right Column - Editable Form -->
                <div class="lg:col-span-2">
                    <form @submit.prevent="updateProfile" class="space-y-6">
                        <!-- Personal Information -->
                        <div class="rounded-2xl bg-white p-6 shadow-md">
                            <div class="mb-6 flex items-center justify-between border-b border-gray-100 pb-4">
                                <h3 class="flex items-center gap-2 text-lg font-bold text-gray-800">
                                    <i class="fa-solid fa-user text-green-600"></i>
                                    Personal Information
                                </h3>
                                <button
                                    type="button"
                                    @click="isEditing = !isEditing"
                                    class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-sm font-medium transition"
                                    :class="isEditing ? 'bg-red-50 text-red-600 hover:bg-red-100' : 'bg-green-50 text-green-600 hover:bg-green-100'"
                                >
                                    <i :class="isEditing ? 'fa-solid fa-xmark' : 'fa-solid fa-pen'"></i>
                                    {{ isEditing ? 'Cancel' : 'Edit' }}
                                </button>
                            </div>

                            <div class="grid gap-6 md:grid-cols-2">
                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-gray-700">Full Name</label>
                                    <div class="relative">
                                        <i class="fa-solid fa-user absolute top-1/2 left-3 -translate-y-1/2 text-gray-400"></i>
                                        <input
                                            v-model="user.name"
                                            type="text"
                                            :disabled="!isEditing"
                                            class="w-full rounded-lg border border-gray-200 py-2.5 pr-4 pl-10 transition focus:border-green-500 focus:ring-2 focus:ring-green-100 focus:outline-none disabled:bg-gray-50 disabled:text-gray-600"
                                            placeholder="Enter your name"
                                        />
                                    </div>
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-gray-700">Email Address</label>
                                    <div class="relative">
                                        <i class="fa-solid fa-envelope absolute top-1/2 left-3 -translate-y-1/2 text-gray-400"></i>
                                        <input
                                            v-model="user.email"
                                            type="email"
                                            disabled
                                            class="w-full rounded-lg border border-gray-200 bg-gray-50 py-2.5 pr-4 pl-10 text-gray-600"
                                            placeholder="your@email.com"
                                        />
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">Email cannot be changed</p>
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-gray-700">Phone Number</label>
                                    <div class="relative">
                                        <i class="fa-solid fa-phone absolute top-1/2 left-3 -translate-y-1/2 text-gray-400"></i>
                                        <input
                                            v-model="user.mobile_number"
                                            type="text"
                                            :disabled="!isEditing"
                                            class="w-full rounded-lg border border-gray-200 py-2.5 pr-4 pl-10 transition focus:border-green-500 focus:ring-2 focus:ring-green-100 focus:outline-none disabled:bg-gray-50 disabled:text-gray-600"
                                            placeholder="+63 900 000 0000"
                                        />
                                    </div>
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-gray-700">Address</label>
                                    <div class="relative">
                                        <i class="fa-solid fa-location-dot absolute top-1/2 left-3 -translate-y-1/2 text-gray-400"></i>
                                        <input
                                            v-model="user.address"
                                            type="text"
                                            :disabled="!isEditing"
                                            class="w-full rounded-lg border border-gray-200 py-2.5 pr-4 pl-10 transition focus:border-green-500 focus:ring-2 focus:ring-green-100 focus:outline-none disabled:bg-gray-50 disabled:text-gray-600"
                                            placeholder="Enter your address"
                                        />
                                    </div>
                                </div>
                            </div>

                            <div v-if="isEditing" class="mt-6 flex justify-end gap-3">
                                <button
                                    type="button"
                                    @click="cancelEdit"
                                    class="rounded-lg border border-gray-300 px-6 py-2.5 font-medium text-gray-700 transition hover:bg-gray-50"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    class="flex items-center gap-2 rounded-lg bg-gradient-to-r from-green-600 to-green-500 px-6 py-2.5 font-semibold text-white shadow-md transition hover:from-green-700 hover:to-green-600"
                                >
                                    <i class="fa-solid fa-check"></i>
                                    Save Changes
                                </button>
                            </div>
                        </div>

                        <!-- Account Statistics -->
                        <div class="grid gap-4 sm:grid-cols-3">
                            <div class="rounded-xl bg-gradient-to-br from-blue-50 to-blue-100 p-4 shadow-sm">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-500 text-white">
                                        <i class="fa-solid fa-calendar-check"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-blue-700">Account Age</p>
                                        <p class="text-lg font-bold text-blue-900">{{ getAccountAge() }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-xl bg-gradient-to-br from-purple-50 to-purple-100 p-4 shadow-sm">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-purple-500 text-white">
                                        <i class="fa-solid fa-shield-check"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-purple-700">Security</p>
                                        <p class="text-lg font-bold text-purple-900">Protected</p>
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-xl bg-gradient-to-br from-green-50 to-green-100 p-4 shadow-sm">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-green-500 text-white">
                                        <i class="fa-solid fa-star"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-green-700">Status</p>
                                        <p class="text-lg font-bold text-green-900">Active</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Enhanced OTP Modal -->
        <transition name="fade">
            <div v-if="showOtpModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4 backdrop-blur-sm">
                <div class="animate-fadeIn w-full max-w-md rounded-2xl bg-white p-8 shadow-2xl">
                    <button @click="closeOtpModal" class="absolute top-4 right-4 text-gray-400 transition hover:text-gray-700">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>

                    <!-- Progress Bar -->
                    <div class="mb-8">
                        <div class="mb-3 flex items-center justify-between">
                            <span class="text-sm font-semibold text-gray-700">
                                {{ otpVerified ? 'Set New Password' : otpSent ? 'Verify OTP' : 'Send Verification Code' }}
                            </span>
                            <span class="text-xs text-gray-500">Step {{ otpVerified ? 3 : otpSent ? 2 : 1 }} of 3</span>
                        </div>
                        <div class="h-2 w-full overflow-hidden rounded-full bg-gray-100">
                            <div
                                class="h-full rounded-full bg-gradient-to-r from-green-600 to-green-500 transition-all duration-500"
                                :style="{ width: otpVerified ? '100%' : otpSent ? '66%' : '33%' }"
                            ></div>
                        </div>
                    </div>

                    <!-- Step 1: Send OTP -->
                    <div v-if="!otpSent" class="space-y-4">
                        <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-green-100 text-green-600">
                            <i class="fa-solid fa-mobile-screen-button text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Verify Your Identity</h3>
                        <p class="text-sm text-gray-600">We'll send a verification code to your phone number.</p>
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-gray-700">Phone Number</label>
                            <input
                                v-model="phone"
                                type="text"
                                class="w-full rounded-lg border border-gray-200 px-4 py-3 transition focus:border-green-500 focus:ring-2 focus:ring-green-100 focus:outline-none"
                                placeholder="+63 900 000 0000"
                            />
                        </div>
                        <button
                            @click="sendOtp"
                            class="w-full rounded-lg bg-gradient-to-r from-green-600 to-green-500 px-4 py-3 font-semibold text-white shadow-md transition hover:from-green-700 hover:to-green-600"
                        >
                            Send Verification Code
                        </button>
                    </div>

                    <!-- Step 2: Verify OTP -->
                    <div v-else-if="!otpVerified" class="space-y-4">
                        <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                            <i class="fa-solid fa-keyboard text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Enter Verification Code</h3>
                        <p class="text-sm text-gray-600">
                            We sent a 6-digit code to <strong class="text-gray-800">{{ phone }}</strong>
                        </p>
                        <div>
                            <input
                                v-model="otp"
                                maxlength="6"
                                type="text"
                                class="w-full rounded-lg border-2 border-gray-200 px-4 py-4 text-center text-2xl font-bold tracking-[0.5em] transition focus:border-green-500 focus:ring-2 focus:ring-green-100 focus:outline-none"
                                placeholder="000000"
                            />
                        </div>
                        <button
                            @click="verifyOtp"
                            class="w-full rounded-lg bg-gradient-to-r from-green-600 to-green-500 px-4 py-3 font-semibold text-white shadow-md transition hover:from-green-700 hover:to-green-600"
                        >
                            Verify Code
                        </button>
                        <button @click="sendOtp" class="w-full text-sm text-green-600 hover:text-green-700">Didn't receive code? Resend</button>
                    </div>

                    <!-- Step 3: Change Password -->
                    <div v-else class="space-y-4">
                        <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-green-100 text-green-600">
                            <i class="fa-solid fa-check-circle text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Create New Password</h3>
                        <p class="text-sm text-gray-600">Choose a strong password to protect your account.</p>
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-gray-700">New Password</label>
                            <input
                                v-model="newPassword"
                                type="password"
                                class="w-full rounded-lg border border-gray-200 px-4 py-3 transition focus:border-green-500 focus:ring-2 focus:ring-green-100 focus:outline-none"
                                placeholder="Enter new password"
                            />
                        </div>
                        <button
                            @click="changePassword"
                            class="w-full rounded-lg bg-gradient-to-r from-green-600 to-green-500 px-4 py-3 font-semibold text-white shadow-md transition hover:from-green-700 hover:to-green-600"
                        >
                            Save New Password
                        </button>
                    </div>
                </div>
            </div>
        </transition>
    </MainLayout>
</template>

<script setup>
import MainLayout from '@/layouts/MainLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, ref } from 'vue';

const user = ref({});
const userImage = ref('');
const photoPreview = ref(null);
const isEditing = ref(false);
const originalUser = ref({});

const fetchProfile = async () => {
    try {
        const { data } = await axios.get(route('users.profileData'));
        if (data.result) {
            user.value = data.data;
            originalUser.value = { ...data.data };
            userImage.value = data.data.profile_photo_url;
        }
    } catch (e) {
        console.error(e);
    }
};

onMounted(() => fetchProfile());

const refreshProfile = () => window.location.reload();

const cancelEdit = () => {
    user.value = { ...originalUser.value };
    isEditing.value = false;
    photoPreview.value = null;
};

function formatDate(dateString) {
    if (!dateString) return 'No date set';
    const date = new Date(dateString);
    return isNaN(date.getTime()) ? 'Invalid date' : date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

function getAccountAge() {
    if (!user.value.created_at) return 'N/A';
    const created = new Date(user.value.created_at);
    const now = new Date();
    const months = Math.floor((now - created) / (1000 * 60 * 60 * 24 * 30));
    if (months < 12) return `${months} months`;
    const years = Math.floor(months / 12);
    return `${years} year${years > 1 ? 's' : ''}`;
}

const previewPhoto = (e) => {
    const file = e.target.files[0];
    if (file) {
        photoPreview.value = URL.createObjectURL(file);
        isEditing.value = true;
    }
};

const updateProfile = async () => {
    const formData = new FormData();
    formData.append('name', user.value.name);
    formData.append('mobile_number', user.value.mobile_number);
    formData.append('address', user.value.address);

    const photoFile = document.querySelector('#profilePhoto').files[0] || document.querySelector('#visibleProfilePhoto').files[0];
    if (photoFile) {
        formData.append('profile_photo', photoFile);
    }

    try {
        const response = await axios.post(route('user.profile.update'), formData, { headers: { 'Content-Type': 'multipart/form-data' } });
        if (response.data.result) {
            alert('Profile updated successfully!');
            userImage.value = response.data.data.profile_photo_url;
            originalUser.value = { ...user.value };
            photoPreview.value = null;
            isEditing.value = false;
        } else {
            alert(response.data.message);
        }
    } catch (error) {
        console.error(error);
        alert('Failed to update profile');
    }
};

const showOtpModal = ref(false);
const otpSent = ref(false);
const otpVerified = ref(false);
const phone = ref('');
const otp = ref('');
const newPassword = ref('');

const openOtpModal = () => {
    showOtpModal.value = true;
    otpSent.value = false;
    otpVerified.value = false;
    phone.value = user.value.mobile_number || '';
    otp.value = '';
    newPassword.value = '';
};

const closeOtpModal = () => {
    showOtpModal.value = false;
    otpSent.value = false;
    otpVerified.value = false;
};

const sendOtp = async () => {
    try {
        const response = await axios.post(route('otp.send'), { phone: phone.value });
        if (response.data.result) {
            otpSent.value = true;
            alert(response.data.message);
        } else {
            alert(response.data.message);
        }
    } catch (error) {
        alert('Failed to send OTP');
    }
};

const verifyOtp = async () => {
    try {
        const response = await axios.post(route('otp.verify'), { otp: otp.value });
        if (response.data.result) {
            otpVerified.value = true;
            alert('OTP verified. You can now change your password.');
        } else {
            alert('Invalid OTP.');
        }
    } catch (error) {
        alert('Failed to verify OTP');
    }
};

const changePassword = async () => {
    try {
        const response = await axios.post(route('user.password.update'), { password: newPassword.value });
        if (response.data.result) {
            alert('Password changed successfully!');
            closeOtpModal();
        } else {
            alert(response.data.message);
        }
    } catch (error) {
        alert('Failed to change password');
    }
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.animate-fadeIn {
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
