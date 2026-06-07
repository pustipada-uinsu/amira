<script setup>
import { Button } from '@/Components/ui/button';
import { Card, CardContent } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import InputError from '@/Components/InputError.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';

const page = usePage()

const form = useForm({
    email: '',
    password: '',
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

const redirectGoogle = () => {
  window.location.href = route('auth.google')
}
</script>

<template>
  <GuestLayout>
    <Head title="Login Admin" />

    <div class="flex items-center justify-center px-4">

      <div class="w-full max-w-md">

        <!-- ERROR -->
        <div
          v-if="page.props.flash?.error"
          class="mb-4 rounded-md bg-red-50 p-3 text-sm text-red-700 border border-red-200"
        >
          {{ page.props.flash.error }}
        </div>

        <Card class="shadow-lg border border-gray-200">
          <CardContent class="p-8">

            <form @submit.prevent="submit" class="flex flex-col gap-6">

              <!-- HEADER -->
              <div class="text-center">
                <h1 class="text-3xl font-bold text-emerald-700">
                  Admin AMIRA
                </h1>
                <p class="mt-2 text-sm text-gray-500">
                  Login untuk mengelola registrasi acara
                </p>
              </div>

              <!-- USERNAME -->
              <div class="grid gap-2">
                <Label for="username">Username</Label>
                <Input
                  id="email"
                  type="text"
                  v-model="form.email"
                  required
                  autofocus
                />
                <InputError :message="form.errors.email" />
              </div>

              <!-- PASSWORD -->
              <div class="grid gap-2">
                <Label for="password">Password</Label>
                <Input
                  id="password"
                  type="password"
                  v-model="form.password"
                  required
                />
                <InputError :message="form.errors.password" />
              </div>

              <!-- LOGIN BUTTON -->
              <Button
                type="submit"
                class="w-full bg-emerald-600 hover:bg-emerald-700 text-white"
                :disabled="form.processing"
              >
                Masuk
              </Button>

              <!-- GOOGLE LOGIN -->
              <Button
                type="button"
                variant="outline"
                class="w-full flex items-center justify-center gap-2"
                @click="redirectGoogle"
              >
                <svg class="h-5 w-5" viewBox="0 0 533.5 544.3">
                  <path fill="#4285f4" d="M533.5 278.4c0-17.4-1.6-34.1-4.6-50.4H272.1v95.4h147c-6.3 34.3-25 63.4-53.4 83v68h86.4c50.6-46.6 81.4-115.3 81.4-196z"/>
                  <path fill="#34a853" d="M272.1 544.3c72.4 0 133.1-23.9 177.5-64.9l-86.4-68c-24 16.1-54.7 25.6-91.1 25.6-69.9 0-129.1-47.2-150.4-110.7h-89.5v69.5c44.2 87.3 135.6 148.5 239.9 148.5z"/>
                  <path fill="#fbbc04" d="M121.7 326.3c-10.9-32.8-10.9-68.2 0-101l-89.5-69.5c-39.1 77.9-39.1 162.6 0 240.5z"/>
                  <path fill="#ea4335" d="M272.1 107.7c38.6 0 73.2 13.3 100.5 39.5l75.4-75.4C405.2 24.6 344.5 0 272.1 0 167.8 0 76.3 61.2 32.1 148.5l89.6 69.5c21.3-63.5 80.5-110.7 150.4-110.7z"/>
                </svg>
                Login dengan Google
              </Button>

            </form>

          </CardContent>
        </Card>

        <!-- FOOTER -->
        <p class="text-center text-xs text-gray-500 mt-4">
          © AMIRA - Aplikasi Manajemen Informasi & Registrasi Acara | UINSU - 2026
        </p>

      </div>
    </div>
  </GuestLayout>
</template>