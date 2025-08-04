<script setup lang="ts">
import AppLogoSecondary from '@/components/AppLogoSecondary.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Link, useForm } from '@inertiajs/vue3';
import * as icons from 'lucide-vue-next';
import { ToastAction, ToastDescription, ToastProvider, ToastRoot, ToastTitle, ToastViewport } from 'reka-ui';
import type { FunctionalComponent } from 'vue';
import { computed, ref } from 'vue';
import bulletsCover from '../../assets/images/bullets-cover.svg';

const open = ref(false);
const eventDateRef = ref(new Date());
const timerRef = ref(0);

function oneWeekAway() {
    const now = new Date();
    const inOneWeek = now.setDate(now.getDate() + 7);
    return new Date(inOneWeek);
}

function popToast() {
    open.value = false;
    window.clearTimeout(timerRef.value);
    timerRef.value = window.setTimeout(() => {
        eventDateRef.value = oneWeekAway();
        open.value = true;
    }, 100);
}

type StepInfo = {
    title: string;
    tag: string;
    icon: string;
};

const stepData: StepInfo[] = [
    { title: 'Gun Club Information', tag: 'Provide your gun club information', icon: 'Goal' },
    { title: 'Review & Submit', tag: 'Review your details before submitting.', icon: 'CheckCircle' },
];

const step = ref<number>(0);

const stepTitle = computed(() => stepData[step.value]?.title ?? '');
const stepTag = computed(() => stepData[step.value]?.tag ?? '');
const stepIcon = computed(() => stepData[step.value]?.icon ?? '');

const totalSteps = stepData.length;

function nextStep(): void {
    formValidator();
    if (step.value < totalSteps - 1 && isFormValid.value == true) {
        step.value++;
    }
}

function prevStep(): void {
    if (step.value > 0) {
        step.value--;
    }
}

const IconComponent = computed<FunctionalComponent>(() => {
    const iconName = stepIcon.value;
    return icons[iconName as keyof typeof icons] as FunctionalComponent;
});

const form = useForm({
    name: '',
    address: '',
    contact_person: '',
    contact_no: '',
    email_address: '',
    logo: null as File | null,
});

const isFormValid = ref<boolean | null>(null);

function formValidator(): void {
    if (!form.name || !form.address || !form.contact_person || !form.contact_no || !form.email_address || !form.logo) {
        isFormValid.value = false;
        return;
    }

    isFormValid.value = true;
}

function formatPhone(e: any) {
    let input = e.target.value;

    // Remove all non-digit characters
    input = input.replace(/\D/g, '');

    // Limit to 11 digits
    input = input.substring(0, 11);

    // Format as 4-3-4 (e.g. 0997 243 0944)
    if (input.length >= 8) {
        input = input.replace(/(\d{4})(\d{3})(\d{0,4})/, '$1 $2 $3');
    } else if (input.length >= 5) {
        input = input.replace(/(\d{4})(\d{0,3})/, '$1 $2');
    }

    form.contact_no = input.trim();
}

const logoError = ref('');

function handleFile(e: Event) {
    const target = e.target as HTMLInputElement;
    const file = target.files?.[0] || null;

    logoError.value = '';

    if (file && file.size > 2 * 1024 * 1024) {
        logoError.value = 'File size must not exceed 2MB.';
        form.logo = null;
        return;
    }

    form.logo = file;
}

const isSubmitSuccess = ref(false);

const submit = () => {
    form.post(route('register-gunclub.store'), {
        forceFormData: true,
        onSuccess: () => {
            popToast();
            //form.reset();
            logoError.value = '';
            isSubmitSuccess.value = true;
        },
        onError: (errors) => {
            step.value = 0;
            isSubmitSuccess.value = false;
            console.error('Validation failed:', errors);
        },
    });
};
</script>

<template>
    <Head title="Register">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="flex h-screen w-screen flex-col text-[#1b1b18] lg:flex-row dark:bg-zinc-900 dark:text-white">
        <div
            class="relative flex w-full flex-col items-center justify-between overflow-hidden bg-zinc-100 p-10 lg:h-full lg:w-[580px] lg:items-start lg:bg-zinc-200 dark:bg-zinc-900"
        >
            <!-- Background image layer with opacity -->
            <div class="absolute inset-0 hidden bg-cover bg-center opacity-5 lg:block" :style="`background-image: url(${bulletsCover})`"></div>

            <!-- Foreground content -->
            <div class="relative z-10 flex h-full flex-col justify-between">
                <div class="flex w-fit items-center justify-start space-x-2">
                    <img src="../../assets/images/logo.png" alt="Logo" class="h-16" />
                    <AppLogoSecondary class="w-64 text-zinc-950 dark:text-zinc-50" />
                </div>

                <div class="h-full lg:mt-8">
                    <h4 class="h-fit text-center text-xl font-medium text-zinc-950 lg:text-start dark:text-zinc-50">Gunclun Registration</h4>
                    <div class="mx-5 mt-8 hidden flex-col items-start justify-start space-y-2 lg:flex">
                        <div class="flex w-full items-center justify-between space-x-2">
                            <div class="flex h-10 w-14 items-center justify-center rounded-lg bg-zinc-50 dark:bg-zinc-800">
                                <icons.Goal />
                            </div>
                            <div class="flex w-full flex-col justify-start">
                                <h4 class="text-sm font-bold">Gun Club Information</h4>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Provide your gun club information</p>
                            </div>
                            <span v-if="isFormValid == null">
                                <icons.Loader2 class="animate-spin" :size="24" />
                            </span>
                            <span v-else-if="isFormValid">
                                <icons.Check class="text-green-600" :size="24" />
                            </span>
                            <span v-else>
                                <icons.X class="text-red-600" :size="24" />
                            </span>
                        </div>

                        <div class="flex w-12 items-center justify-center">
                            <div class="mr-0.5 h-8 w-0.5 rounded-full bg-zinc-800"></div>
                        </div>

                        <div class="flex w-full items-center justify-between space-x-2">
                            <div class="flex h-10 w-14 items-center justify-center rounded-lg bg-zinc-50 dark:bg-zinc-800">
                                <icons.CheckCircle />
                            </div>
                            <div class="flex w-full flex-col justify-start">
                                <h4 class="text-sm font-bold">Review & Submit</h4>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Confirm and finish</p>
                            </div>
                            <span v-if="form.processing">
                                <icons.Loader2 class="animate-spin" :size="24" />
                            </span>
                            <span v-else-if="form.wasSuccessful">
                                <icons.Check class="text-green-600" :size="24" />
                            </span>
                            <span v-else-if="form.hasErrors">
                                <icons.X class="text-red-600" :size="24" />
                            </span>
                        </div>
                    </div>
                </div>

                <Link
                    :href="route('home')"
                    class="mt-5 flex h-10 items-center justify-center space-x-2 text-zinc-900 hover:text-primary lg:mt-10 lg:w-32 dark:text-zinc-50 dark:hover:text-primary"
                >
                    <icons.ArrowLeft />
                    <span class="cursor-default">Back to main</span>
                </Link>
            </div>
        </div>

        <div class="flex w-full items-center justify-center bg-zinc-100 p-5 lg:h-full dark:bg-zinc-900 lg:dark:bg-zinc-950/20">
            <div class="w-full px-0 lg:px-32">
                <div class="hidden lg:flex">
                    <div class="flex items-center justify-center space-x-2">
                        <div class="rounded bg-zinc-200 p-2 dark:bg-zinc-800">
                            <component :is="IconComponent" class="icon" />
                        </div>

                        <div class="flex flex-col">
                            <h4 class="text-lg font-bold">{{ stepTitle }}</h4>
                            <p class="-mt-1 text-zinc-600 dark:text-zinc-400">{{ stepTag }}</p>
                        </div>
                    </div>
                </div>
                <form @submit.prevent="submit" class="mt-10 flex flex-col">
                    <div v-if="step == 0" class="grid grid-cols-2 gap-6 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="name">Gun Club Name</Label>
                            <Input
                                id="name"
                                type="text"
                                class="capitalize"
                                required
                                autofocus
                                :tabindex="1"
                                autocomplete="name"
                                v-model="form.name"
                            />
                            <InputError :message="form.errors.name" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="address">Address</Label>
                            <Input
                                id="address"
                                type="text"
                                class="capitalize"
                                required
                                autofocus
                                :tabindex="1"
                                autocomplete="address"
                                v-model="form.address"
                            />
                            <InputError :message="form.errors.address" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="contact_person">Contact Person</Label>
                            <Input
                                id="contact_person"
                                type="text"
                                required
                                autofocus
                                :tabindex="1"
                                autocomplete="contact_person"
                                v-model="form.contact_person"
                            />
                            <InputError :message="form.errors.contact_person" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="contact_no">Contact Number</Label>
                            <Input
                                id="contact_no"
                                type="text"
                                required
                                autofocus
                                :tabindex="1"
                                autocomplete="contact_no"
                                v-model="form.contact_no"
                                @input="formatPhone"
                            />
                            <InputError :message="form.errors.contact_no" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="email_address">Email Address</Label>
                            <Input
                                id="email_address"
                                type="text"
                                required
                                autofocus
                                :tabindex="1"
                                autocomplete="email_address"
                                v-model="form.email_address"
                            />
                            <InputError :message="form.errors.email_address" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="logo">Logo</Label>
                            <Input
                                id="logo"
                                type="file"
                                accept="image/jpeg,image/png"
                                required
                                autofocus
                                :tabindex="1"
                                autocomplete="logo"
                                @change="handleFile"
                            />
                            <InputError :message="form.errors.logo || logoError" />
                        </div>
                    </div>
                    <div v-if="step == 1" class="flex flex-col items-center justify-center lg:w-full">
                        <div v-if="form.wasSuccessful == false" class="w-full rounded-md bg-yellow-50 p-4 text-lg text-yellow-800">
                            <strong>ᡕᠵデᡁ᠊╾━💥 Double-check your details so everything hits the mark — no one likes a misfire.</strong>
                        </div>
                        <div v-if="form.wasSuccessful" class="mb-5 rounded-md bg-green-950 p-4 text-lg text-green-100">
                            <strong>Submission Successful!</strong>
                            <p class="text-basetext-justify mt-2">
                                Your information has been received and is now being processed. If you need to make any changes, a secure update link
                                has been sent to the email address you provided. Please check your inbox (and spam folder) for further instructions.
                            </p>
                        </div>
                        <ToastProvider>
                            <ToastRoot
                                v-model:open="open"
                                class="data-[state=open]:animate-slideIn data-[state=closed]:animate-hide data-[swipe=end]:animate-swipeOut grid grid-cols-[auto_max-content] items-center gap-x-[15px] rounded-lg border bg-white p-[15px] shadow-sm [grid-template-areas:_'title_action'_'description_action'] data-[swipe=cancel]:translate-x-0 data-[swipe=cancel]:transition-[transform_200ms_ease-out] data-[swipe=move]:translate-x-[var(--reka-toast-swipe-move-x)] dark:bg-zinc-700"
                            >
                                <ToastTitle class="text-slate12 mb-[5px] text-sm font-medium [grid-area:_title]"> Submit Success!</ToastTitle>
                                <ToastDescription as-child>
                                    For any corrections or updates, please use the link that will be sent to your submitted email address.
                                </ToastDescription>
                                <ToastAction class="[grid-area:_action]" as-child alt-text="Goto schedule to undo">
                                    <button
                                        class="bg-green2 text-green11 shadow-green7 hover:shadow-green8 focus:shadow-green8 inline-flex h-[25px] items-center justify-center rounded-md px-[10px] text-xs leading-[25px] font-medium shadow-[inset_0_0_0_1px] hover:shadow-[inset_0_0_0_1px] focus:shadow-[0_0_0_2px]"
                                    >
                                        Close
                                    </button>
                                </ToastAction>
                            </ToastRoot>
                            <ToastViewport
                                class="fixed right-0 bottom-0 z-[2147483647] m-0 flex w-[390px] max-w-[100vw] list-none flex-col gap-[10px] p-[var(--viewport-padding)] outline-none [--viewport-padding:_25px]"
                            />
                        </ToastProvider>
                    </div>

                    <div
                        v-if="isSubmitSuccess == false"
                        class="mt-8 flex flex-row items-center justify-end space-y-4 space-x-1 sm:flex-row sm:space-y-0 sm:space-x-4 lg:flex-row lg:space-x-4"
                    >
                        <Button
                            type="button"
                            v-if="step > 0"
                            @click="prevStep()"
                            class="mt-4 h-10 w-1/2 bg-zinc-400 text-white hover:bg-zinc-400 sm:mt-0 lg:w-52 dark:bg-zinc-700"
                            :tabindex="4"
                        >
                            <icons.ArrowLeft class="h-4 w-4" />
                            Previous
                        </Button>

                        <Button v-if="step < 1" type="button" @click="nextStep()" class="h-10 w-1/2 text-white lg:w-52" :tabindex="4">
                            Next
                            <icons.ArrowRight class="h-4 w-4" />
                        </Button>
                        <Button v-if="step == 1" class="h-10 w-1/2 text-white lg:w-52" :tabindex="4" :disabled="form.processing">
                            <template v-if="form.processing">
                                <icons.LoaderCircle class="mr-2 h-4 w-4 animate-spin" />
                                Submitting…
                            </template>
                            <template v-else>
                                Submit
                                <icons.Check class="ml-2 h-4 w-4" />
                            </template>
                        </Button>
                    </div>
                    <Link v-if="form.wasSuccessful" :href="route('home')">
                        <Button
                            type="button"
                            v-if="step > 0"
                            @click="form.reset()"
                            class="mt-10 h-10 w-full bg-zinc-400 text-white hover:bg-zinc-400 sm:mt-0 sm:w-52 dark:bg-zinc-700"
                            :tabindex="4"
                        >
                            <icons.ArrowLeft class="h-4 w-4" />
                            Back to main
                        </Button>
                    </Link>
                </form>
            </div>
        </div>
    </div>
</template>
