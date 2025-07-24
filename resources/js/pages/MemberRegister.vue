<script setup lang="ts">
import AppLogoSecondary from '@/components/AppLogoSecondary.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Link, useForm } from '@inertiajs/vue3';
import * as icons from 'lucide-vue-next';
import {
    RadioGroupIndicator,
    RadioGroupItem,
    RadioGroupRoot,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectItemIndicator,
    SelectItemText,
    SelectLabel,
    SelectPortal,
    SelectRoot,
    SelectScrollDownButton,
    SelectScrollUpButton,
    SelectTrigger,
    SelectValue,
    SelectViewport,
} from 'reka-ui';
import type { FunctionalComponent } from 'vue';
import { computed, ref } from 'vue';
import bulletsCover from '../../assets/images/bullets-cover.svg';

const radioStateSingle = ref('default');

type StepInfo = {
    title: string;
    tag: string;
    icon: string;
};

const stepData: StepInfo[] = [
    { title: 'Application Type', tag: 'Choose New or Renewal', icon: 'FilePlus' },
    { title: 'Personal Information', tag: 'Tell us about yourself', icon: 'UserRound' },
    { title: 'Home Address', tag: 'Verify your address', icon: 'MapPinHouse' },
    { title: 'Work Details', tag: 'Tell us about your work', icon: 'BriefcaseBusiness' },
    { title: 'Photo & Signature', tag: 'Verify your identity', icon: 'Signature' },
    { title: 'Gun Club', tag: 'Select your gun club', icon: 'Goal' },
    { title: 'Firearms Details', tag: 'Indicate your firearms', icon: 'Crosshair' },
    { title: 'Review & Submit', tag: 'Confirm and finish', icon: 'CheckCircle' },
];

const step = ref<number>(1);

const stepTitle = computed(() => stepData[step.value]?.title ?? '');
const stepTag = computed(() => stepData[step.value]?.tag ?? '');
const stepIcon = computed(() => stepData[step.value]?.icon ?? '');

const totalSteps = stepData.length;

function nextStep(): void {
    if (step.value < totalSteps - 1) {
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
    reg_type: '',
    application_venue: '',
    last_name: '',
    first_name: '',
    middle_name: '',
    extension: '',
    email: '',
    phone: '',
    birth_date: '',
    birth_place: '',
    age: '',
    gender: '',
    civil_status: '',
    blood_type: '',
    street: '',
    purok: '',
    barangay: '',
    city_municipality: '',
    province: '',
    occupation: '',
    company_organization: '',
    position: '',
    office_business_address: '',
    office_landline: '',
    office_email: '',
    licensed_shooter: false,
    photo: '',
    signature: '',
    recommended_by: '',
    approved_by: '',
});

const genderOptions = ['Male', 'Female', 'Others'];
const civilStatusOptions = ['Single', 'Married', 'Separated', 'Divorced', 'Widowed'];
const bloodTypeOptions = [
    'O+',
    'O−',
    'A+',
    'A−',
    'B+',
    'B−',
    'AB+',
    'AB−',
    'A1',
    'A2',
    'A1B',
    'A2B',
    'Bombay (hh)',
    'Rh-null',
    'In(Lu)',
    'K0 (Kell null)',
    'D−−',
    'Jk(a−b−) (Kidd null)',
    'McLeod phenotype',
    'Sd(a−)',
    'Lutheran null (Lu−−)',
    'Vel−',
    'Colton null',
    'Yt(a−)',
    'Jr(a−)',
    'Lan−',
];

const submit = () => {
    form.post(route('register'), {
        forceFormData: true,
        onSuccess: () => form.reset(),
        onError: (errors) => {
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
    <div class="flex h-screen w-screen text-[#1b1b18] dark:text-white">
        <div class="relative flex h-full w-[580px] flex-col justify-between overflow-hidden bg-zinc-200 p-10 dark:bg-zinc-900">
            <!-- Background image layer with opacity -->
            <div class="absolute inset-0 bg-cover bg-center opacity-5" :style="`background-image: url(${bulletsCover})`"></div>

            <!-- Foreground content -->
            <div class="relative z-10 flex h-full flex-col justify-between">
                <div class="flex w-fit items-center justify-start space-x-2">
                    <img src="../../assets/images/logo.png" alt="Logo" class="h-16" />
                    <AppLogoSecondary class="w-64 text-zinc-950 dark:text-zinc-50" />
                </div>

                <div>
                    <h4 class="h-fit text-xl font-medium text-zinc-950 dark:text-zinc-50">Member Registration</h4>
                    <div class="mx-5 mt-8 flex flex-col items-start justify-start space-y-2">
                        <div class="flex w-full items-center justify-between space-x-2">
                            <div class="flex h-10 w-14 items-center justify-center rounded-lg bg-zinc-50 dark:bg-zinc-800">
                                <icons.FilePlus class="text-primary" />
                            </div>
                            <div class="flex w-full flex-col justify-start">
                                <h2 class="text-sm font-bold">Application Type</h2>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">How you'd like to proceed?</p>
                            </div>
                            <icons.LoaderCircle class="animate-spin" :size="32" />
                        </div>

                        <div class="flex w-12 items-center justify-center">
                            <div class="mr-0.5 h-8 w-0.5 rounded-full bg-zinc-800"></div>
                        </div>

                        <div class="flex w-full items-center justify-between space-x-2">
                            <div class="flex h-10 w-14 items-center justify-center rounded-lg bg-zinc-50 dark:bg-zinc-800">
                                <icons.UserRound />
                            </div>
                            <div class="flex w-full flex-col justify-start">
                                <h2 class="text-sm font-bold">Personal Information</h2>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Tell us about yourself</p>
                            </div>
                            <icons.LoaderCircle class="animate-spin" :size="32" />
                        </div>

                        <div class="flex w-12 items-center justify-center">
                            <div class="mr-0.5 h-8 w-0.5 rounded-full bg-zinc-800"></div>
                        </div>

                        <div class="flex w-full items-center justify-between space-x-2">
                            <div class="flex h-10 w-14 items-center justify-center rounded-lg bg-zinc-50 dark:bg-zinc-800">
                                <icons.MapPinHouse />
                            </div>
                            <div class="flex w-full flex-col justify-start">
                                <h2 class="text-sm font-bold">Home Address</h2>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Verify your address</p>
                            </div>
                            <icons.LoaderCircle class="animate-spin" :size="32" />
                        </div>

                        <div class="flex w-12 items-center justify-center">
                            <div class="mr-0.5 h-8 w-0.5 rounded-full bg-zinc-800"></div>
                        </div>

                        <div class="flex w-full items-center justify-between space-x-2">
                            <div class="flex h-10 w-14 items-center justify-center rounded-lg bg-zinc-50 dark:bg-zinc-800">
                                <icons.BriefcaseBusiness />
                            </div>
                            <div class="flex w-full flex-col justify-start">
                                <h2 class="text-sm font-bold">Work Details</h2>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Tell us about your work</p>
                            </div>
                            <icons.CircleCheck class="text-green-600" :size="32" />
                        </div>

                        <div class="flex w-12 items-center justify-center">
                            <div class="mr-0.5 h-8 w-0.5 rounded-full bg-zinc-800"></div>
                        </div>

                        <div class="flex w-full items-center justify-between space-x-2">
                            <div class="flex h-10 w-14 items-center justify-center rounded-lg bg-zinc-50 dark:bg-zinc-800">
                                <icons.Signature />
                            </div>
                            <div class="flex w-full flex-col justify-start">
                                <h4 class="text-sm font-bold">Photo & Signature</h4>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Verify your identity</p>
                            </div>
                            <icons.CircleX class="text-primary" :size="32" />
                        </div>

                        <div class="flex w-12 items-center justify-center">
                            <div class="mr-0.5 h-8 w-0.5 rounded-full bg-zinc-800"></div>
                        </div>

                        <div class="flex w-full items-center justify-between space-x-2">
                            <div class="flex h-10 w-14 items-center justify-center rounded-lg bg-zinc-50 dark:bg-zinc-800">
                                <icons.Goal />
                            </div>
                            <div class="flex w-full flex-col justify-start">
                                <h4 class="text-sm font-bold">Gun Club</h4>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Select your gun club</p>
                            </div>
                            <icons.Check :size="32" />
                        </div>

                        <div class="flex w-12 items-center justify-center">
                            <div class="mr-0.5 h-8 w-0.5 rounded-full bg-zinc-800"></div>
                        </div>

                        <div class="flex w-full items-center justify-between space-x-2">
                            <div class="flex h-10 w-14 items-center justify-center rounded-lg bg-zinc-50 dark:bg-zinc-800">
                                <icons.Crosshair />
                            </div>
                            <div class="flex w-full flex-col justify-start">
                                <h4 class="text-sm font-bold">Firearms Details</h4>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Indicate your firearms</p>
                            </div>
                            <icons.Check :size="32" />
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
                            <icons.Check :size="32" />
                        </div>
                    </div>
                </div>

                <Link
                    :href="route('home')"
                    class="mt-10 flex h-10 w-32 items-center justify-center space-x-2 text-zinc-900 hover:text-primary/80 dark:text-zinc-50"
                >
                    <icons.ArrowLeft />
                    <span class="cursor-default">Back to main</span>
                </Link>
            </div>
        </div>

        <div class="flex h-full w-full items-center justify-center bg-zinc-100 p-5 dark:bg-zinc-900/80">
            <div>
                <div class="flex">
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
                    <div v-if="step == 0" class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                        <RadioGroupRoot v-model="radioStateSingle" class="flex flex-col gap-2.5" default-value="default" aria-label="View density">
                            <div class="flex items-center">
                                <RadioGroupItem
                                    id="r1"
                                    class="h-[1.125rem] w-[1.125rem] cursor-default rounded-full border bg-white shadow-sm outline-none focus:shadow-[0_0_0_2px] focus:shadow-stone-700 data-[active=true]:border-stone-700 data-[active=true]:bg-stone-700 dark:data-[active=true]:bg-white"
                                    value="default"
                                >
                                    <RadioGroupIndicator
                                        class="relative flex h-full w-full items-center justify-center after:block after:h-2 after:w-2 after:rounded-[50%] after:bg-white after:content-[''] dark:after:bg-stone-700"
                                    />
                                </RadioGroupItem>
                                <label class="pl-[15px] text-sm leading-none text-stone-700 dark:text-white" for="r1"> Default </label>
                            </div>
                            <div class="flex items-center">
                                <RadioGroupItem
                                    id="r2"
                                    class="h-[1.125rem] w-[1.125rem] cursor-default rounded-full border bg-white shadow-sm outline-none focus:shadow-[0_0_0_2px] focus:shadow-stone-700 data-[active=true]:border-stone-700 data-[active=true]:bg-stone-700 dark:data-[active=true]:bg-white"
                                    value="comfortable"
                                >
                                    <RadioGroupIndicator
                                        class="relative flex h-full w-full items-center justify-center after:block after:h-2 after:w-2 after:rounded-[50%] after:bg-white after:content-[''] dark:after:bg-stone-700"
                                    />
                                </RadioGroupItem>
                                <label class="pl-[15px] text-sm leading-none text-stone-700 dark:text-white" for="r2"> Comfortable </label>
                            </div>
                            <div class="flex items-center">
                                <RadioGroupItem
                                    id="r3"
                                    class="h-[1.125rem] w-[1.125rem] cursor-default rounded-full border bg-white shadow-sm outline-none focus:shadow-[0_0_0_2px] focus:shadow-stone-700 data-[active=true]:border-stone-700 data-[active=true]:bg-stone-700 dark:data-[active=true]:bg-white"
                                    value="compact"
                                >
                                    <RadioGroupIndicator
                                        class="relative flex h-full w-full items-center justify-center after:block after:h-2 after:w-2 after:rounded-[50%] after:bg-white after:content-[''] dark:after:bg-stone-700"
                                    />
                                </RadioGroupItem>
                                <label class="pl-[15px] text-sm leading-none text-stone-700 dark:text-white" for="r3"> Compact </label>
                            </div>
                        </RadioGroupRoot>
                        <InputError :message="form.errors.email" />
                    </div>

                    <div v-if="step == 1" class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                        <div class="grid gap-2">
                            <Label for="lastname">Lastname</Label>
                            <Input id="lastname" type="text" required autofocus :tabindex="1" autocomplete="lastname" v-model="form.last_name" />
                            <InputError :message="form.errors.last_name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="firstname">Firstname</Label>
                            <Input id="firstname" type="text" required autofocus :tabindex="1" autocomplete="firstname" v-model="form.first_name" />
                            <InputError :message="form.errors.first_name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="middlename">Middlename</Label>
                            <Input
                                id="middlename"
                                type="text"
                                required
                                autofocus
                                :tabindex="1"
                                autocomplete="middlename"
                                v-model="form.middle_name"
                            />
                            <InputError :message="form.errors.middle_name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="extension">Extension</Label>
                            <Input id="extension" type="text" required autofocus :tabindex="1" autocomplete="extension" v-model="form.extension" />
                            <InputError :message="form.errors.extension" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="birth_date">Birth Date</Label>
                            <Input id="birth_date" type="date" required autofocus :tabindex="1" autocomplete="birth_date" v-model="form.birth_date" />
                            <InputError :message="form.errors.birth_date" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="birth_place">Birth Place</Label>
                            <Input
                                id="birth_place"
                                type="text"
                                required
                                autofocus
                                :tabindex="1"
                                autocomplete="birth_place"
                                v-model="form.birth_place"
                            />
                            <InputError :message="form.errors.birth_place" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="age">Age</Label>
                            <Input id="age" type="number" required autofocus :tabindex="1" autocomplete="age" v-model="form.age" />
                            <InputError :message="form.errors.age" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="gender">Gender</Label>
                            <SelectRoot v-model="form.gender">
                                <SelectTrigger
                                    class="text-grass11 data-[placeholder]:text-green9 inline-flex h-10 min-w-[160px] items-center justify-between gap-[5px] rounded-lg border px-[15px] text-xs leading-none shadow-sm outline-none hover:bg-stone-50 focus:shadow-[0_0_0_2px] focus:shadow-black dark:bg-input/30"
                                    aria-label="Customise options"
                                >
                                    <SelectValue placeholder="" />
                                    <icons.ChevronDown icon="radix-icons:chevron-down" class="h-3.5 w-3.5" />
                                </SelectTrigger>

                                <SelectPortal>
                                    <SelectContent
                                        class="data-[side=top]:animate-slideDownAndFade data-[side=right]:animate-slideLeftAndFade data-[side=bottom]:animate-slideUpAndFade data-[side=left]:animate-slideRightAndFade z-[100] min-w-[160px] rounded-lg border bg-zinc-100 shadow-sm will-change-[opacity,transform] dark:bg-zinc-800"
                                        :side-offset="5"
                                    >
                                        <SelectScrollUpButton
                                            class="text-violet11 flex h-[25px] cursor-default items-center justify-center bg-zinc-200 dark:bg-zinc-700"
                                        >
                                            <icons.ArrowUp :size="16" />
                                        </SelectScrollUpButton>

                                        <SelectViewport class="p-[5px]">
                                            <SelectLabel class="text-mauve11 px-[25px] text-xs leading-[25px] font-medium"> Gender </SelectLabel>
                                            <SelectGroup>
                                                <SelectItem
                                                    v-for="(option, index) in genderOptions"
                                                    :key="index"
                                                    class="text-grass11 data-[disabled]:text-mauve8 data-[highlighted]:bg-green9 data-[highlighted]:text-green1 relative flex h-[25px] items-center rounded-[3px] pr-[35px] pl-[25px] text-xs leading-none select-none hover:bg-zinc-300 data-[disabled]:pointer-events-none data-[highlighted]:outline-none hover:dark:bg-zinc-700"
                                                    :value="option"
                                                >
                                                    <SelectItemIndicator class="absolute left-0 inline-flex w-[25px] items-center justify-center">
                                                        <Icon icon="radix-icons:check" />
                                                    </SelectItemIndicator>
                                                    <SelectItemText>
                                                        {{ option }}
                                                    </SelectItemText>
                                                </SelectItem>
                                            </SelectGroup>
                                        </SelectViewport>

                                        <SelectScrollDownButton
                                            class="text-violet11 flex h-[25px] cursor-default items-center justify-center bg-zinc-200 dark:bg-zinc-700"
                                        >
                                            <icons.ArrowDown :size="16" />
                                        </SelectScrollDownButton>
                                    </SelectContent>
                                </SelectPortal>
                            </SelectRoot>
                            <InputError :message="form.errors.gender" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="civil_status">Civil Status</Label>
                            <SelectRoot v-model="form.civil_status">
                                <SelectTrigger
                                    class="text-grass11 data-[placeholder]:text-green9 inline-flex h-10 min-w-[160px] items-center justify-between gap-[5px] rounded-lg border px-[15px] text-xs leading-none shadow-sm outline-none hover:bg-stone-50 focus:shadow-[0_0_0_2px] focus:shadow-black dark:bg-input/30"
                                    aria-label="Customise options"
                                >
                                    <SelectValue placeholder="" />
                                    <icons.ChevronDown icon="radix-icons:chevron-down" class="h-3.5 w-3.5" />
                                </SelectTrigger>

                                <SelectPortal>
                                    <SelectContent
                                        class="data-[side=top]:animate-slideDownAndFade data-[side=right]:animate-slideLeftAndFade data-[side=bottom]:animate-slideUpAndFade data-[side=left]:animate-slideRightAndFade z-[100] min-w-[160px] rounded-lg border bg-zinc-100 shadow-sm will-change-[opacity,transform] dark:bg-zinc-800"
                                        :side-offset="5"
                                    >
                                        <SelectScrollUpButton
                                            class="text-violet11 flex h-[25px] cursor-default items-center justify-center bg-zinc-200 dark:bg-zinc-700"
                                        >
                                            <icons.ArrowUp :size="16" />
                                        </SelectScrollUpButton>

                                        <SelectViewport class="p-[5px]">
                                            <SelectLabel class="text-mauve11 px-[25px] text-xs leading-[25px] font-medium">
                                                Civil Status
                                            </SelectLabel>
                                            <SelectGroup>
                                                <SelectItem
                                                    v-for="(option, index) in civilStatusOptions"
                                                    :key="index"
                                                    class="text-grass11 data-[disabled]:text-mauve8 data-[highlighted]:bg-green9 data-[highlighted]:text-green1 relative flex h-[25px] items-center rounded-[3px] pr-[35px] pl-[25px] text-xs leading-none select-none hover:bg-zinc-300 data-[disabled]:pointer-events-none data-[highlighted]:outline-none hover:dark:bg-zinc-700"
                                                    :value="option"
                                                >
                                                    <SelectItemIndicator class="absolute left-0 inline-flex w-[25px] items-center justify-center">
                                                        <Icon icon="radix-icons:check" />
                                                    </SelectItemIndicator>
                                                    <SelectItemText>
                                                        {{ option }}
                                                    </SelectItemText>
                                                </SelectItem>
                                            </SelectGroup>
                                        </SelectViewport>

                                        <SelectScrollDownButton
                                            class="text-violet11 flex h-[25px] cursor-default items-center justify-center bg-zinc-200 dark:bg-zinc-700"
                                        >
                                            <icons.ArrowDown :size="16" />
                                        </SelectScrollDownButton>
                                    </SelectContent>
                                </SelectPortal>
                            </SelectRoot>
                            <InputError :message="form.errors.civil_status" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="blood_type">Blood Type</Label>
                            <SelectRoot v-model="form.blood_type">
                                <SelectTrigger
                                    class="text-grass11 data-[placeholder]:text-green9 inline-flex h-10 min-w-[160px] items-center justify-between gap-[5px] rounded-lg border px-[15px] text-xs leading-none shadow-sm outline-none hover:bg-stone-50 focus:shadow-[0_0_0_2px] focus:shadow-black dark:bg-input/30"
                                    aria-label="Customise options"
                                >
                                    <SelectValue placeholder="" />
                                    <icons.ChevronDown icon="radix-icons:chevron-down" class="h-3.5 w-3.5" />
                                </SelectTrigger>

                                <SelectPortal>
                                    <SelectContent
                                        class="data-[side=top]:animate-slideDownAndFade data-[side=right]:animate-slideLeftAndFade data-[side=bottom]:animate-slideUpAndFade data-[side=left]:animate-slideRightAndFade z-[100] min-w-[160px] rounded-lg border bg-zinc-100 shadow-sm will-change-[opacity,transform] dark:bg-zinc-800"
                                        :side-offset="5"
                                    >
                                        <SelectScrollUpButton
                                            class="text-violet11 flex h-[25px] cursor-default items-center justify-center bg-zinc-200 dark:bg-zinc-700"
                                        >
                                            <icons.ArrowUp :size="16" />
                                        </SelectScrollUpButton>

                                        <SelectViewport class="p-[5px]">
                                            <SelectLabel class="text-mauve11 px-[25px] text-xs leading-[25px] font-medium"> Blood Type </SelectLabel>
                                            <SelectGroup>
                                                <SelectItem
                                                    v-for="(option, index) in bloodTypeOptions"
                                                    :key="index"
                                                    class="text-grass11 data-[disabled]:text-mauve8 data-[highlighted]:bg-green9 data-[highlighted]:text-green1 relative flex h-[25px] items-center rounded-[3px] pr-[35px] pl-[25px] text-xs leading-none select-none hover:bg-zinc-300 data-[disabled]:pointer-events-none data-[highlighted]:outline-none hover:dark:bg-zinc-700"
                                                    :value="option"
                                                >
                                                    <SelectItemIndicator class="absolute left-0 inline-flex w-[25px] items-center justify-center">
                                                        <Icon icon="radix-icons:check" />
                                                    </SelectItemIndicator>
                                                    <SelectItemText>
                                                        {{ option }}
                                                    </SelectItemText>
                                                </SelectItem>
                                            </SelectGroup>
                                        </SelectViewport>

                                        <SelectScrollDownButton
                                            class="text-violet11 flex h-[25px] cursor-default items-center justify-center bg-zinc-200 dark:bg-zinc-700"
                                        >
                                            <icons.ArrowDown :size="16" />
                                        </SelectScrollDownButton>
                                    </SelectContent>
                                </SelectPortal>
                            </SelectRoot>
                            <InputError :message="form.errors.blood_type" />
                        </div>
                    </div>

                    <div v-if="step == 2" class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                        <div class="grid gap-2">
                            <Label for="street">Street</Label>
                            <Input id="street" type="text" required autofocus :tabindex="1" autocomplete="street" v-model="form.street" />
                            <InputError :message="form.errors.street" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="purok">Purok</Label>
                            <Input id="purok" type="text" required autofocus :tabindex="1" autocomplete="purok" v-model="form.purok" />
                            <InputError :message="form.errors.purok" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="barangay">Barangay</Label>
                            <Input id="barangay" type="text" required autofocus :tabindex="1" autocomplete="barangay" v-model="form.barangay" />
                            <InputError :message="form.errors.barangay" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="city_municipality">City/Municipality</Label>
                            <Input
                                id="city_municipality"
                                type="text"
                                required
                                autofocus
                                :tabindex="1"
                                autocomplete="city_municipality"
                                v-model="form.city_municipality"
                            />
                            <InputError :message="form.errors.city_municipality" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="province">Province</Label>
                            <Input id="province" type="text" required autofocus :tabindex="1" autocomplete="province" v-model="form.province" />
                            <InputError :message="form.errors.province" />
                        </div>
                    </div>

                    <div v-if="step == 3" class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                        <div class="grid gap-2">
                            <Label for="occupation">Occupation</Label>
                            <Input id="occupation" type="text" required autofocus :tabindex="1" autocomplete="occupation" v-model="form.occupation" />
                            <InputError :message="form.errors.occupation" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="company_organization">Company/Organization</Label>
                            <Input
                                id="company_organization"
                                type="text"
                                required
                                autofocus
                                :tabindex="1"
                                autocomplete="purok"
                                v-model="form.company_organization"
                            />
                            <InputError :message="form.errors.company_organization" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="position">Position</Label>
                            <Input id="position" type="text" required autofocus :tabindex="1" autocomplete="position" v-model="form.position" />
                            <InputError :message="form.errors.position" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="office_business_address">Office/Business</Label>
                            <Input
                                id="office_business_address"
                                type="text"
                                required
                                autofocus
                                :tabindex="1"
                                autocomplete="office_business_address"
                                v-model="form.office_business_address"
                            />
                            <InputError :message="form.errors.office_business_address" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="office_landline">Landline</Label>
                            <Input
                                id="office_landline"
                                type="text"
                                required
                                autofocus
                                :tabindex="1"
                                autocomplete="office_landline"
                                v-model="form.office_landline"
                            />
                            <InputError :message="form.errors.office_landline" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="office_email">Email</Label>
                            <Input
                                id="office_email"
                                type="text"
                                required
                                autofocus
                                :tabindex="1"
                                autocomplete="office_email"
                                v-model="form.office_email"
                            />
                            <InputError :message="form.errors.office_email" />
                        </div>
                    </div>

                    <div class="mt-8 flex flex-col-reverse items-center justify-end space-y-4 space-x-0 sm:flex-row sm:space-y-0 sm:space-x-4">
                        <Button
                            type="button"
                            v-if="step > 0"
                            @click="prevStep()"
                            class="mt-4 h-10 w-full bg-zinc-400 text-white hover:bg-zinc-400 sm:mt-0 sm:w-52 dark:bg-zinc-700"
                            :tabindex="4"
                        >
                            <icons.ArrowLeft class="h-4 w-4" />
                            Previous
                        </Button>

                        <Button v-if="step < 7" type="button" @click="nextStep()" class="h-10 w-full text-white sm:w-52" :tabindex="4">
                            Next
                            <icons.ArrowRight class="h-4 w-4" />
                        </Button>
                        <Button v-if="step == 7" type="button" @click="nextStep()" class="h-10 w-52 text-white" :tabindex="4">
                            Submit
                            <icons.Check class="h-4 w-4" />
                        </Button>
                    </div>

                    <!-- <Button type="submit" class="mt-4 w-52 text-white" :tabindex="4" :disabled="form.processing">
                        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                        Next
                    </Button> -->
                </form>
            </div>
        </div>
    </div>
</template>
