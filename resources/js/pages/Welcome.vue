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
          <div class="text-center lg:text-left">
            <h2 class="text-4xl font-bold tracking-tight mb-4">
              Simplifying Community Contributions for Your Household
            </h2>
            <p class="text-muted-foreground text-lg mb-6 max-w-md mx-auto lg:mx-0">
              Bringing neighbors together by making shared expenses transparent, fair, and easy to manage. Join your community in building a better, more connected neighborhood.
            </p>
            <div class="flex flex-col sm:flex-row justify-center lg:justify-start gap-4">
              <Link
                v-if="!user"
                :href="route('register')"
                class="bg-primary text-black px-6 py-2 rounded-xl font-medium hover:bg-primary/90"
              >
                Join Our Community
              </Link>
              <Link
                :href="user ? '/pay' : route('login')"
                class="border border-muted px-6 py-2 rounded-xl hover:border-primary hover:text-primary"
              >
                Make a Payment
              </Link>
            </div>
          </div>

          <!-- Hero Image -->
          <div class="w-full lg:w-1/2">
            <div class="w-full h-64 lg:h-80 bg-muted rounded-xl shadow-md flex items-center justify-center text-muted-foreground text-xl">
              [ Community Image Placeholder ]
            </div>
          </div>
        </div>
      </main>

      <!-- About Our Community Section -->
      <section class="bg-muted/10 py-16 px-6">
        <div class="max-w-4xl mx-auto text-center">
          <h3 class="text-3xl font-semibold mb-4">About Our Neighborhood</h3>
          <p class="text-muted-foreground text-lg leading-relaxed">
            Our community thrives on cooperation and mutual support. This platform was created to simplify how households manage shared expenses — like utilities, maintenance fees, and community events. Transparency and fairness are our priorities, so everyone knows exactly where their contributions go.
          </p>
        </div>
      </section>

      <!-- How It Works Section -->
      <section class="py-16 px-6 bg-background border-t border-muted">
        <div class="max-w-5xl mx-auto text-center">
          <h3 class="text-3xl font-semibold mb-10">How It Works</h3>
          <div class="grid gap-12 md:grid-cols-3">
            <div>
              <div class="text-primary text-5xl mb-3">👥</div>
              <h4 class="font-semibold mb-2">Join the Community</h4>
              <p class="text-muted-foreground text-sm max-w-xs mx-auto">
                Register your household and become part of the neighborhood network.
              </p>
            </div>
            <div>
              <div class="text-primary text-5xl mb-3">💵</div>
              <h4 class="font-semibold mb-2">Make Contributions</h4>
              <p class="text-muted-foreground text-sm max-w-xs mx-auto">
                Easily pay your share of communal expenses through secure and convenient methods.
              </p>
            </div>
            <div>
              <div class="text-primary text-5xl mb-3">📋</div>
              <h4 class="font-semibold mb-2">Track & Review</h4>
              <p class="text-muted-foreground text-sm max-w-xs mx-auto">
                Stay informed with clear reports and updates on how funds are used in your neighborhood.
              </p>
            </div>
          </div>
        </div>
      </section>

      <!-- Community Testimonials Section -->
      <section class="py-16 px-6 bg-muted/10">
        <div class="max-w-4xl mx-auto text-center">
          <h3 class="text-3xl font-semibold mb-8">What Our Neighbors Say</h3>
          <div class="space-y-8">
            <blockquote class="italic text-muted-foreground max-w-2xl mx-auto">
              “This system made it so easy for my family to keep up with our monthly contributions. No more confusion or missed payments!”
              <footer class="mt-2 font-semibold">– Maria, Resident</footer>
            </blockquote>
            <blockquote class="italic text-muted-foreground max-w-2xl mx-auto">
              “Transparent and convenient. I love that I can check exactly where my payments go and participate in community decisions.”
              <footer class="mt-2 font-semibold">– Juan, Community Leader</footer>
            </blockquote>
          </div>
        </div>
      </section>
    </div>
  </MainLayout>
</template>
