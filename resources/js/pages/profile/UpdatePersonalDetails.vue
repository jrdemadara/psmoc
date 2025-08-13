<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useForm } from '@inertiajs/vue3';
import { ArrowDown, ArrowUp, ChevronDown, LoaderCircle } from 'lucide-vue-next';
import {
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
    ToastAction,
    ToastDescription,
    ToastProvider,
    ToastRoot,
    ToastTitle,
    ToastViewport,
} from 'reka-ui';
import { PropType, ref } from 'vue';

interface Data {
    last_name: string;
    first_name: string;
    middle_name: string | '';
    extension: string | 'None';
    birth_date: string;
    birth_place: string;
    age: number;
    gender: string;
    civil_status: string;
    blood_type: string;
    email: string;
    phone: string;
}

const props = defineProps({
    token: {
        type: String,
        required: true,
    },
    data: {
        type: Object as PropType<Data>,
        required: true,
    },
});

const capitalize = (str?: string) => (str ? str.charAt(0).toUpperCase() + str.slice(1).toLowerCase() : '');

const form = useForm({
    token: props.token,
    last_name: props.data.last_name,
    first_name: props.data.first_name,
    middle_name: props.data.middle_name,
    extension: props.data.extension,
    email: props.data.email,
    phone: props.data.phone,
    birth_date: props.data.birth_date,
    birth_place: props.data.birth_place,
    age: props.data.age,
    gender: capitalize(props.data.gender),
    civil_status: capitalize(props.data.civil_status),
    blood_type: capitalize(props.data.blood_type),
});

const formErrorMessage = ref<string>('');

const submit = () => {
    form.patch(route('update-personal-details'), {
        onSuccess: () => {
            popSuccessToast();
            formErrorMessage.value = '';
        },
        onError: (errors) => {
            formErrorMessage.value = Object.values(errors)[0]?.[0] ?? 'Something went wrong';
        },
    });
};

const genderOptions = ['Male', 'Female'];
const extensionOptions = ['None', 'Sr.', 'Jr.', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X'];
const civilStatusOptions = ['Single', 'Married', 'Separated', 'Divorced', 'Widowed'];
const bloodTypeOptions = ['O+', 'O−', 'A+', 'A−', 'B+', 'B−', 'AB+', 'AB−', 'A1', 'A2', 'A1B', 'A2B'];

const successToastOpen = ref(false);
const timerRef = ref(0);

function popSuccessToast() {
    successToastOpen.value = false;
    window.clearTimeout(timerRef.value);
    timerRef.value = window.setTimeout(() => {
        successToastOpen.value = true;
    }, 100);
}

function formatPhone(e: any) {
    let input = e.target.value;
    input = input.replace(/\D/g, '');
    input = input.substring(0, 11);
    if (input.length >= 8) {
        input = input.replace(/(\d{4})(\d{3})(\d{0,4})/, '$1 $2 $3');
    } else if (input.length >= 5) {
        input = input.replace(/(\d{4})(\d{0,3})/, '$1 $2');
    }

    form.phone = input.trim();
}
</script>

<template>
    <div class="flex">
        <form @submit.prevent="submit" class="flex w-full flex-col space-y-10">
            <h4 class="text-xl font-semibold text-zinc-50">Personal Details</h4>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                <div class="grid gap-2">
                    <Label for="lastname">Lastname</Label>
                    <Input id="lastname" type="text" class="capitalize" required autofocus autocomplete="lastname" v-model="form.last_name" />
                    <InputError :message="form.errors.last_name" />
                </div>

                <div class="grid gap-2">
                    <Label for="firstname">Firstname</Label>
                    <Input id="firstname" type="text" class="capitalize" required autofocus autocomplete="firstname" v-model="form.first_name" />
                    <InputError :message="form.errors.first_name" />
                </div>

                <div class="grid gap-2">
                    <Label for="middlename">Middlename</Label>
                    <Input id="middlename" type="text" class="capitalize" autofocus autocomplete="middlename" v-model="form.middle_name" />
                    <InputError :message="form.errors.middle_name" />
                </div>

                <div class="grid gap-2">
                    <Label for="civil_status">Extension</Label>
                    <SelectRoot v-model="form.extension">
                        <SelectTrigger
                            class="data-[placeholder]:text-green9 inline-flex h-10 min-w-[160px] items-center justify-between gap-[5px] rounded-lg border px-[15px] text-xs leading-none shadow-sm outline-none hover:bg-stone-50 focus:shadow-[0_0_0_2px] focus:shadow-black dark:bg-input/30"
                            aria-label="Customise options"
                        >
                            <SelectValue placeholder="" />
                            <ChevronDown icon="radix-icons:chevron-down" class="h-3.5 w-3.5" />
                        </SelectTrigger>

                        <SelectPortal>
                            <SelectContent
                                class="data-[side=top]:animate-slideDownAndFade data-[side=right]:animate-slideLeftAndFade data-[side=bottom]:animate-slideUpAndFade data-[side=left]:animate-slideRightAndFade z-[100] min-w-[160px] rounded-lg border bg-zinc-100 shadow-sm will-change-[opacity,transform] dark:bg-zinc-800"
                                :side-offset="5"
                            >
                                <SelectScrollUpButton class="flex h-[25px] cursor-default items-center justify-center bg-zinc-200 dark:bg-zinc-700">
                                    <ArrowUp :size="16" />
                                </SelectScrollUpButton>

                                <SelectViewport class="p-[5px]">
                                    <SelectLabel class="px-[25px] text-xs leading-[25px] font-medium"> Extension </SelectLabel>
                                    <SelectGroup>
                                        <SelectItem
                                            v-for="(option, index) in extensionOptions"
                                            :key="index"
                                            class="data-[highlighted]:text-green1 relative flex h-[25px] items-center rounded-[3px] pr-[35px] pl-[25px] text-xs leading-none select-none hover:bg-zinc-300 data-[disabled]:pointer-events-none data-[highlighted]:outline-none hover:dark:bg-zinc-700"
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

                                <SelectScrollDownButton class="flex h-[25px] cursor-default items-center justify-center bg-zinc-200 dark:bg-zinc-700">
                                    <ArrowDown :size="16" />
                                </SelectScrollDownButton>
                            </SelectContent>
                        </SelectPortal>
                    </SelectRoot>
                    <InputError :message="form.errors.extension" />
                </div>

                <div class="grid gap-2">
                    <Label for="birth_date">Birth Date</Label>
                    <Input id="birth_date" type="date" class="capitalize" required autofocus autocomplete="birth_date" v-model="form.birth_date" />
                    <InputError :message="form.errors.birth_date" />
                </div>

                <div class="grid gap-2">
                    <Label for="birth_place">Birth Place</Label>
                    <Input id="birth_place" type="text" class="capitalize" required autofocus autocomplete="birth_place" v-model="form.birth_place" />
                    <InputError :message="form.errors.birth_place" />
                </div>

                <div class="grid gap-2">
                    <Label for="age">Age</Label>
                    <Input id="age" type="number" required autofocus autocomplete="age" v-model="form.age" />
                    <InputError :message="form.errors.age" />
                </div>

                <div class="grid gap-2">
                    <Label for="gender">Gender</Label>
                    <SelectRoot v-model="form.gender">
                        <SelectTrigger
                            class="data-[placeholder]:text-green9 inline-flex h-10 min-w-[160px] items-center justify-between gap-[5px] rounded-lg border px-[15px] text-xs leading-none shadow-sm outline-none hover:bg-stone-50 focus:shadow-[0_0_0_2px] focus:shadow-black dark:bg-input/30"
                            aria-label="Customise options"
                        >
                            <SelectValue placeholder="" />
                            <ChevronDown icon="radix-icons:chevron-down" class="h-3.5 w-3.5" />
                        </SelectTrigger>

                        <SelectPortal>
                            <SelectContent
                                class="data-[side=top]:animate-slideDownAndFade data-[side=right]:animate-slideLeftAndFade data-[side=bottom]:animate-slideUpAndFade data-[side=left]:animate-slideRightAndFade z-[100] min-w-[160px] rounded-lg border bg-zinc-100 shadow-sm will-change-[opacity,transform] dark:bg-zinc-800"
                                :side-offset="5"
                            >
                                <SelectScrollUpButton class="flex h-[25px] cursor-default items-center justify-center bg-zinc-200 dark:bg-zinc-700">
                                    <ArrowUp :size="16" />
                                </SelectScrollUpButton>

                                <SelectViewport class="p-[5px]">
                                    <SelectLabel class="px-[25px] text-xs leading-[25px] font-medium"> Gender </SelectLabel>
                                    <SelectGroup>
                                        <SelectItem
                                            v-for="(option, index) in genderOptions"
                                            :key="index"
                                            class="data-[highlighted]:text-green1 relative flex h-[25px] cursor-default items-center rounded-[3px] pr-[35px] pl-[25px] text-xs leading-none select-none hover:bg-zinc-300 data-[disabled]:pointer-events-none data-[highlighted]:outline-none hover:dark:bg-zinc-700"
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

                                <SelectScrollDownButton class="flex h-[25px] cursor-default items-center justify-center bg-zinc-200 dark:bg-zinc-700">
                                    <ArrowDown :size="16" />
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
                            class="data-[placeholder]:text-green9 inline-flex h-10 min-w-[160px] items-center justify-between gap-[5px] rounded-lg border px-[15px] text-xs leading-none shadow-sm outline-none hover:bg-stone-50 focus:shadow-[0_0_0_2px] focus:shadow-black dark:bg-input/30"
                            aria-label="Customise options"
                        >
                            <SelectValue placeholder="" />
                            <ChevronDown icon="radix-icons:chevron-down" class="h-3.5 w-3.5" />
                        </SelectTrigger>

                        <SelectPortal>
                            <SelectContent
                                class="data-[side=top]:animate-slideDownAndFade data-[side=right]:animate-slideLeftAndFade data-[side=bottom]:animate-slideUpAndFade data-[side=left]:animate-slideRightAndFade z-[100] min-w-[160px] rounded-lg border bg-zinc-100 shadow-sm will-change-[opacity,transform] dark:bg-zinc-800"
                                :side-offset="5"
                            >
                                <SelectScrollUpButton class="flex h-[25px] cursor-default items-center justify-center bg-zinc-200 dark:bg-zinc-700">
                                    <ArrowUp :size="16" />
                                </SelectScrollUpButton>

                                <SelectViewport class="p-[5px]">
                                    <SelectLabel class="px-[25px] text-xs leading-[25px] font-medium"> Civil Status </SelectLabel>
                                    <SelectGroup>
                                        <SelectItem
                                            v-for="(option, index) in civilStatusOptions"
                                            :key="index"
                                            class="data-[highlighted]:text-green1 relative flex h-[25px] items-center rounded-[3px] pr-[35px] pl-[25px] text-xs leading-none select-none hover:bg-zinc-300 data-[disabled]:pointer-events-none data-[highlighted]:outline-none hover:dark:bg-zinc-700"
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

                                <SelectScrollDownButton class="flex h-[25px] cursor-default items-center justify-center bg-zinc-200 dark:bg-zinc-700">
                                    <ArrowDown :size="16" />
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
                            class="data-[placeholder]:text-green9 inline-flex h-10 min-w-[160px] items-center justify-between gap-[5px] rounded-lg border px-[15px] text-xs leading-none shadow-sm outline-none hover:bg-stone-50 focus:shadow-[0_0_0_2px] focus:shadow-black dark:bg-input/30"
                            aria-label="Customise options"
                        >
                            <SelectValue placeholder="" />
                            <ChevronDown icon="radix-icons:chevron-down" class="h-3.5 w-3.5" />
                        </SelectTrigger>

                        <SelectPortal>
                            <SelectContent
                                class="data-[side=top]:animate-slideDownAndFade data-[side=right]:animate-slideLeftAndFade data-[side=bottom]:animate-slideUpAndFade data-[side=left]:animate-slideRightAndFade z-[100] min-w-[160px] rounded-lg border bg-zinc-100 shadow-sm will-change-[opacity,transform] dark:bg-zinc-800"
                                :side-offset="5"
                            >
                                <SelectScrollUpButton class="flex h-[25px] cursor-default items-center justify-center bg-zinc-200 dark:bg-zinc-700">
                                    <ArrowUp :size="16" />
                                </SelectScrollUpButton>

                                <SelectViewport class="p-[5px]">
                                    <SelectLabel class="px-[25px] text-xs leading-[25px] font-medium"> Blood Type </SelectLabel>
                                    <SelectGroup>
                                        <SelectItem
                                            v-for="(option, index) in bloodTypeOptions"
                                            :key="index"
                                            class="data-[highlighted]:text-green1 relative flex h-[25px] items-center rounded-[3px] pr-[35px] pl-[25px] text-xs leading-none select-none hover:bg-zinc-300 data-[disabled]:pointer-events-none data-[highlighted]:outline-none hover:dark:bg-zinc-700"
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

                                <SelectScrollDownButton class="flex h-[25px] cursor-default items-center justify-center bg-zinc-200 dark:bg-zinc-700">
                                    <ArrowDown :size="16" />
                                </SelectScrollDownButton>
                            </SelectContent>
                        </SelectPortal>
                    </SelectRoot>
                    <InputError :message="form.errors.blood_type" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">Email</Label>
                    <Input id="email" type="email" required autofocus autocomplete="email" v-model="form.email" />
                    <InputError :message="form.errors.email" />
                </div>
                <div class="grid gap-2">
                    <Label for="phone">Phone</Label>
                    <Input id="phone" @input="formatPhone" type="text" required autofocus autocomplete="tel" v-model="form.phone" />
                    <InputError :message="form.errors.phone" />
                </div>
            </div>

            <Button class="h-10 text-white lg:w-52" :disabled="form.processing">
                <template v-if="form.processing">
                    <LoaderCircle class="size-5 animate-spin" />
                </template>
                <template v-else> Submit Changes </template>
            </Button>
        </form>
    </div>
    <ToastProvider>
        <ToastRoot
            v-model:open="successToastOpen"
            class="data-[state=open]:animate-slideIn data-[state=closed]:animate-hide data-[swipe=end]:animate-swipeOut grid grid-cols-[auto_max-content] items-center gap-x-[15px] rounded-lg border bg-green-600 p-[15px] shadow-sm [grid-template-areas:_'title_action'_'description_action'] data-[swipe=cancel]:translate-x-0 data-[swipe=cancel]:transition-[transform_200ms_ease-out] data-[swipe=move]:translate-x-[var(--reka-toast-swipe-move-x)]"
        >
            <ToastTitle class="mb-[5px] text-sm font-medium [grid-area:_title]"> Submit Success!</ToastTitle>
            <ToastDescription as-child>
                <p>Your membership profile has been successfully updated.</p>
            </ToastDescription>
            <ToastAction class="[grid-area:_action]" as-child alt-text="Goto schedule to undo">
                <button
                    class="inline-flex h-[25px] items-center justify-center rounded-md px-[10px] text-xs leading-[25px] font-medium shadow-[inset_0_0_0_1px] hover:shadow-[inset_0_0_0_1px] focus:shadow-[0_0_0_2px]"
                >
                    Close
                </button>
            </ToastAction>
        </ToastRoot>
        <ToastViewport
            class="fixed right-0 bottom-0 z-[2147483647] m-0 flex w-[390px] max-w-[100vw] list-none flex-col gap-[10px] p-[var(--viewport-padding)] outline-none [--viewport-padding:_25px]"
        />
    </ToastProvider>
</template>
