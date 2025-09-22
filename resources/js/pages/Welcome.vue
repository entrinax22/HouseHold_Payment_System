<script setup>
import MainLayout from '@/layouts/MainLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';
const page = usePage();
const user = computed(() => page.props.auth.user);
const images = ref([]);
const fetch = async () => {
    try {
        const response = await axios.get(route('gallery'));
        if (response.data.result === true) {
            images.value = response.data.galleryFiles;
        } else {
            console.log(response.data.message);
        }
    } catch (error) {
        console.log(error.messages);
    }
};

onMounted(() => {
    fetch();
});
</script>

<template>
    <Head title="Welcome to Our Neighborhood" />
    <MainLayout>
        <div class="bg-background text-foreground flex min-h-screen flex-col">
            <!-- Hero Section -->
            <main class="flex-grow px-6 py-20">
                <div class="mx-auto flex max-w-6xl flex-col-reverse items-center gap-12 lg:flex-row">
                    <div class="space-y-6 text-center lg:text-left">
                        <h2 class="text-5xl leading-tight font-extrabold tracking-tight">
                            Simplifying Community Contributions <br class="hidden md:block" />
                            for Your Household
                        </h2>
                        <p class="text-muted-foreground mx-auto max-w-md text-lg lg:mx-0">
                            Bringing neighbors together by making shared expenses transparent, fair, and easy to manage. Join your community in
                            building a better, more connected neighborhood.
                        </p>
                        <div class="flex justify-center gap-4 lg:justify-start">
                            <Link
                                href="/register"
                                class="rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 px-8 py-4 font-semibold text-white shadow-lg transition-all duration-200 hover:scale-105 hover:from-emerald-600 hover:to-teal-600 hover:shadow-xl"
                            >
                                Get Started
                            </Link>
                            <Link
                                href="/about"
                                class="rounded-xl border-2 border-blue-500 px-8 py-4 font-semibold text-blue-600 shadow-lg transition-all duration-200 hover:scale-105 hover:bg-blue-500 hover:text-white hover:shadow-xl"
                            >
                                Learn More
                            </Link>
                        </div>
                    </div>

                    <!-- Hero Image -->
                    <div class="w-full lg:w-1/2">
                        <img src="/logo.png" alt="Community" class="h-64 w-full rounded-xl object-cover lg:h-80" />
                    </div>
                </div>
            </main>

            <!-- About Section -->
            <section class="bg-muted/10 px-6 py-20">
                <div class="mx-auto max-w-4xl space-y-6 text-center">
                    <h3 class="text-3xl font-bold">About Our Neighborhood</h3>
                    <p class="text-muted-foreground text-lg leading-relaxed">
                        Our community thrives on cooperation and mutual support. This platform was created to simplify how households manage shared
                        expenses — like utilities, maintenance fees, and community events. Transparency and fairness are our priorities, so everyone
                        knows exactly where their contributions go.
                    </p>
                </div>
            </section>

            <!-- How It Works Section -->
            <section class="bg-background border-muted border-t px-6 py-20">
                <div class="mx-auto max-w-5xl space-y-12 text-center">
                    <h3 class="text-3xl font-bold">How It Works</h3>
                    <div class="grid gap-10 md:grid-cols-3">
                        <div class="bg-card rounded-xl p-6 shadow-sm transition hover:shadow-md">
                            <div class="text-primary mb-4 text-5xl">👥</div>
                            <h4 class="mb-2 text-lg font-semibold">Join the Community</h4>
                            <p class="text-muted-foreground text-sm">Register your household and become part of the neighborhood network.</p>
                        </div>
                        <div class="bg-card rounded-xl p-6 shadow-sm transition hover:shadow-md">
                            <div class="text-primary mb-4 text-5xl">💵</div>
                            <h4 class="mb-2 text-lg font-semibold">Make Contributions</h4>
                            <p class="text-muted-foreground text-sm">
                                Easily pay your share of communal expenses through secure and convenient methods.
                            </p>
                        </div>
                        <div class="bg-card rounded-xl p-6 shadow-sm transition hover:shadow-md">
                            <div class="text-primary mb-4 text-5xl">📋</div>
                            <h4 class="mb-2 text-lg font-semibold">Track & Review</h4>
                            <p class="text-muted-foreground text-sm">
                                Stay informed with clear reports and updates on how funds are used in your neighborhood.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Testimonials Section -->
            <section class="bg-muted/10 px-6 py-20">
                <div class="mx-auto max-w-4xl space-y-12 text-center">
                    <h3 class="text-3xl font-bold">What Our Neighbors Say</h3>
                    <div class="grid gap-8 md:grid-cols-2">
                        <blockquote class="bg-card rounded-xl p-6 shadow-sm">
                            <p class="text-muted-foreground italic">
                                "This system made it so easy for my family to keep up with our monthly contributions. No more confusion or missed
                                payments!"
                            </p>
                            <footer class="mt-4 font-semibold">– Maria, Resident</footer>
                        </blockquote>
                        <blockquote class="bg-card rounded-xl p-6 shadow-sm">
                            <p class="text-muted-foreground italic">
                                "Transparent and convenient. I love that I can check exactly where my payments go and participate in community
                                decisions."
                            </p>
                            <footer class="mt-4 font-semibold">– Juan, Community Leader</footer>
                        </blockquote>
                    </div>
                </div>
            </section>
            <!-- Gallery Section -->
            <section class="bg-background border-muted border-t px-6 py-20">
                <div class="mx-auto max-w-6xl space-y-12 text-center">
                    <h3 class="text-3xl font-bold">Neighborhood Gallery</h3>
                    <p class="text-muted-foreground mx-auto max-w-2xl">
                        A glimpse into our vibrant community — events, gatherings, and the everyday life that brings us together.
                    </p>

                    <!-- ✅ Put grid here, loop inside -->
                    <div class="grid gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                        <div
                            v-for="(image, index) in images"
                            :key="index"
                            class="overflow-hidden rounded-xl shadow-md transition hover:scale-105 hover:shadow-xl"
                        >
                            <img :src="image" alt="Community Event" class="h-48 w-full object-cover" />
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </MainLayout>
</template>
