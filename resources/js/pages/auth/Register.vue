<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});

const submit = () => {
  form.post(route('register'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
  });
};
</script>

<template>
  <Head title="Register" />

  <div class="min-h-screen grid grid-cols-1 md:grid-cols-2">
    <!-- Left Background Image Section -->
    <div
      class="hidden md:block bg-cover bg-center"
      style="background-image: url('/images/bg.jpg');"
    >
      <div class="w-full h-full bg-black/40 flex items-center justify-center p-8">
        <div class="text-white text-center max-w-md">
          <h2 class="text-3xl font-bold mb-4">Join Our Community</h2>
          <p class="text-base">
            Register to start tracking your housing contributions and access important information.
          </p>
        </div>
      </div>
    </div>

    <!-- Right Registration Form Section -->
    <div class="flex items-center justify-center p-6 bg-background">
      <div class="w-full max-w-md space-y-6">
        <h1 class="text-2xl font-bold text-center">Create an Account</h1>
        <p class="text-muted-foreground text-sm text-center">
          Enter your details below to create your account.
        </p>

        <form @submit.prevent="submit" class="space-y-5">
          <div>
            <Label for="name">Name</Label>
            <Input
              id="name"
              type="text"
              required
              autofocus
              autocomplete="name"
              v-model="form.name"
              placeholder="Full name"
            />
            <InputError :message="form.errors.name" />
          </div>

          <div>
            <Label for="email">Email address</Label>
            <Input
              id="email"
              type="email"
              required
              autocomplete="email"
              v-model="form.email"
              placeholder="email@example.com"
            />
            <InputError :message="form.errors.email" />
          </div>

          <div>
            <Label for="password">Password</Label>
            <Input
              id="password"
              type="password"
              required
              autocomplete="new-password"
              v-model="form.password"
              placeholder="Password"
            />
            <InputError :message="form.errors.password" />
          </div>

          <div>
            <Label for="password_confirmation">Confirm password</Label>
            <Input
              id="password_confirmation"
              type="password"
              required
              autocomplete="new-password"
              v-model="form.password_confirmation"
              placeholder="Confirm password"
            />
            <InputError :message="form.errors.password_confirmation" />
          </div>

          <Button type="submit" class="w-full" :disabled="form.processing">
            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2" />
            Create account
          </Button>
        </form>

        <div class="text-center text-sm text-muted-foreground">
          Already have an account?
          <TextLink :href="route('login')" class="underline underline-offset-4">Log in</TextLink>
        </div>
      </div>
    </div>
  </div>
</template>
