<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: boolean;
}>();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('request-member-update.store'));
};
</script>

<template>
    <AuthLayout title="Profile Update Request" description="Enter your email to receive a profile update link">
        <Head title="Profile Update Request" />

        <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
            A new update link has been sent to the email address you provided.
        </div>

        <div v-else-if="status === false" class="mb-4 text-center text-sm font-medium text-red-600">
            No profile found for the provided email address, or the profile is already approved.
        </div>

        <div class="space-y-6">
            <form @submit.prevent="submit">
                <div class="grid gap-2">
                    <Label for="email">Email address</Label>
                    <Input id="email" type="email" name="email" autocomplete="off" v-model="form.email" autofocus placeholder="email@example.com" />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="my-6 flex items-center justify-start">
                    <Button class="w-full text-zinc-50" :disabled="form.processing">
                        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                        Email profile update link
                    </Button>
                </div>
            </form>

            <div class="space-x-1 text-center text-sm text-muted-foreground">
                <span>Or, return to</span>
                <TextLink :href="route('home')">Home</TextLink>
            </div>
        </div>
    </AuthLayout>
</template>
