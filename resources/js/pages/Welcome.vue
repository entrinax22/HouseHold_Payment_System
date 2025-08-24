<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import MainLayout from '@/layouts/MainLayout.vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
</script>

<template>
  <Head title="Welcome to Our Neighborhood" />
  <MainLayout>
    <div class="min-h-screen flex flex-col bg-background text-foreground">
      
      <!-- Hero Section -->
      <main class="flex-grow px-6 py-20">
        <div class="flex flex-col-reverse lg:flex-row items-center gap-12 max-w-6xl mx-auto">
          <div class="text-center lg:text-left space-y-6">
            <h2 class="text-5xl font-extrabold tracking-tight leading-tight">
              Simplifying Community Contributions <br class="hidden md:block" />
              for Your Household
            </h2>
            <p class="text-muted-foreground text-lg max-w-md mx-auto lg:mx-0">
              Bringing neighbors together by making shared expenses transparent, fair, and easy to manage. 
              Join your community in building a better, more connected neighborhood.
            </p>
            <div class="flex justify-center lg:justify-start gap-4">
              <Link
                href="/register"
                class="px-6 py-3 rounded-lg bg-primary text-black font-medium shadow hover:bg-primary/90 transition"
              >
                Get Started
              </Link>
              <Link
                href="/about"
                class="px-6 py-3 rounded-lg border border-muted text-foreground font-medium hover:bg-muted/20 transition"
              >
                Learn More
              </Link>
            </div>
          </div>

          <!-- Hero Image -->
          <div class="w-full lg:w-1/2">
            <img
              src="/images/bg.jpg"
              alt="Community"
              class="w-full h-64 lg:h-80 object-cover rounded-xl shadow-lg"
            />
          </div>
        </div>
      </main>

      <!-- About Section -->
      <section class="bg-muted/10 py-20 px-6">
        <div class="max-w-4xl mx-auto text-center space-y-6">
          <h3 class="text-3xl font-bold">About Our Neighborhood</h3>
          <p class="text-muted-foreground text-lg leading-relaxed">
            Our community thrives on cooperation and mutual support. This platform was created to 
            simplify how households manage shared expenses — like utilities, maintenance fees, and 
            community events. Transparency and fairness are our priorities, so everyone knows exactly 
            where their contributions go.
          </p>
        </div>
      </section>

      <!-- How It Works Section -->
      <section class="py-20 px-6 bg-background border-t border-muted">
        <div class="max-w-5xl mx-auto text-center space-y-12">
          <h3 class="text-3xl font-bold">How It Works</h3>
          <div class="grid gap-10 md:grid-cols-3">
            <div class="p-6 rounded-xl shadow-sm bg-card hover:shadow-md transition">
              <div class="text-primary text-5xl mb-4">👥</div>
              <h4 class="font-semibold text-lg mb-2">Join the Community</h4>
              <p class="text-muted-foreground text-sm">
                Register your household and become part of the neighborhood network.
              </p>
            </div>
            <div class="p-6 rounded-xl shadow-sm bg-card hover:shadow-md transition">
              <div class="text-primary text-5xl mb-4">💵</div>
              <h4 class="font-semibold text-lg mb-2">Make Contributions</h4>
              <p class="text-muted-foreground text-sm">
                Easily pay your share of communal expenses through secure and convenient methods.
              </p>
            </div>
            <div class="p-6 rounded-xl shadow-sm bg-card hover:shadow-md transition">
              <div class="text-primary text-5xl mb-4">📋</div>
              <h4 class="font-semibold text-lg mb-2">Track & Review</h4>
              <p class="text-muted-foreground text-sm">
                Stay informed with clear reports and updates on how funds are used in your neighborhood.
              </p>
            </div>
          </div>
        </div>
      </section>

      <!-- Testimonials Section -->
      <section class="py-20 px-6 bg-muted/10">
        <div class="max-w-4xl mx-auto text-center space-y-12">
          <h3 class="text-3xl font-bold">What Our Neighbors Say</h3>
          <div class="grid gap-8 md:grid-cols-2">
            <blockquote class="p-6 bg-card rounded-xl shadow-sm">
              <p class="italic text-muted-foreground">
                “This system made it so easy for my family to keep up with our monthly contributions. 
                No more confusion or missed payments!”
              </p>
              <footer class="mt-4 font-semibold">– Maria, Resident</footer>
            </blockquote>
            <blockquote class="p-6 bg-card rounded-xl shadow-sm">
              <p class="italic text-muted-foreground">
                “Transparent and convenient. I love that I can check exactly where my payments go and 
                participate in community decisions.”
              </p>
              <footer class="mt-4 font-semibold">– Juan, Community Leader</footer>
            </blockquote>
          </div>
        </div>
      </section>
    </div>
  </MainLayout>
</template>
