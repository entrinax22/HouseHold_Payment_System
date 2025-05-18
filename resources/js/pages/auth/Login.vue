<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
  status?: string;
  canResetPassword: boolean;
}>();

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

const submit = () => {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  });
};
</script>

<template>
  <Head title="Log in" />

  <div class="min-h-screen grid grid-cols-1 md:grid-cols-2">
    <!-- Left Background Image Section -->
    <div
      class="hidden md:block bg-cover bg-center"
      style="background-image: url('/images/bg.jpg');"
    >
      <div class="w-full h-full bg-black/40 flex items-center justify-center p-8">
        <div class="text-white text-center max-w-md">
          <h2 class="text-3xl font-bold mb-4">Welcome to Our Community</h2>
          <p class="text-base">
            Access your account to manage your housing contributions and view important updates.
          </p>
        </div>
      </div>
    </div>

    <!-- Right Login Form Section -->
    <div class="flex items-center justify-center p-6 bg-background">
      <div class="w-full max-w-md space-y-6">
        <h1 class="text-2xl font-bold text-center">Log in to Your Account</h1>
        <p class="text-muted-foreground text-sm text-center">
          Enter your email and password to continue.
        </p>

        <div v-if="status" class="text-center text-sm font-medium text-green-600">
          {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-6">
          <div class="space-y-4">
            <div>
              <Label for="email">Email address</Label>
              <Input
                id="email"
                type="email"
                required
                autofocus
                autocomplete="email"
                v-model="form.email"
                placeholder="email@example.com"
              />
              <InputError :message="form.errors.email" />
            </div>

            <div>
              <div class="flex justify-between items-center">
                <Label for="password">Password</Label>
                <TextLink v-if="canResetPassword" :href="route('password.request')" class="text-sm">
                  Forgot password?
                </TextLink>
              </div>
              <Input
                id="password"
                type="password"
                required
                autocomplete="current-password"
                v-model="form.password"
                placeholder="Password"
              />
              <InputError :message="form.errors.password" />
            </div>

            <div class="flex items-center space-x-2">
              <Checkbox id="remember" v-model="form.remember" />
              <Label for="remember">Remember me</Label>
            </div>
          </div>

          <Button type="submit" class="w-full" :disabled="form.processing">
            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2" />
            Log in
          </Button>
        </form>

        <div class="text-center text-sm text-muted-foreground">
          Don’t have an account?
          <TextLink :href="route('register')">Sign up</TextLink>
        </div>
      </div>
    </div>
  </div>
</template>
