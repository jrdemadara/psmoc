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
    reg_type: string;
    licensed_shooter: string;
    application_venue: string;
    ltopf_no: string;
    license_type: string;
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
    application_venue: props.data.application_venue,
    licensed_shooter: props.data.licensed_shooter ? 'Yes' : 'No',
    ltopf_no: props.data.ltopf_no,
    license_type: capitalize(props.data.license_type),
});

const formErrorMessage = ref<string>('');

const submit = () => {
    form.patch(route('update-application-details'), {
        onSuccess: () => {
            popSuccessToast();
            formErrorMessage.value = '';
        },
        onError: (errors) => {
            formErrorMessage.value = Object.values(errors)[0]?.[0] ?? 'Something went wrong';
        },
    });
};

const licensedShooterOptions = ['Yes', 'No'];
const licenseTypeOptions = ['Type 1', 'Type 2', 'Type 3', 'Type 4', 'Type 5'];

const successToastOpen = ref(false);
const timerRef = ref(0);

function popSuccessToast() {
    successToastOpen.value = false;
    window.clearTimeout(timerRef.value);
    timerRef.value = window.setTimeout(() => {
        successToastOpen.value = true;
    }, 100);
}
</script>

<template>
    <div class="flex">
        <form @submit.prevent="submit" class="flex w-full flex-col space-y-10">
            <h4 class="text-xl font-semibold text-zinc-50">Application Details</h4>

            <div class="grid grid-cols-1 gap-6 text-zinc-50 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="application_venue">Application Venue</Label>
                    <Input
                        id="application_venue"
                        type="text"
                        class="capitalize"
                        required
                        autofocus
                        autocomplete="age"
                        v-model="form.application_venue"
                    />
                    <InputError :message="form.errors.application_venue" />
                </div>
                <div class="grid gap-2">
                    <Label for="gender">Licensed Shooter</Label>
                    <SelectRoot v-model="form.licensed_shooter">
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
                                    <SelectLabel class="px-[25px] text-xs leading-[25px] font-medium"> Licensed Shooter </SelectLabel>
                                    <SelectGroup>
                                        <SelectItem
                                            v-for="(option, index) in licensedShooterOptions"
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
                    <InputError :message="form.errors.licensed_shooter" />
                </div>
                <div class="grid gap-2">
                    <Label for="ltopf_no">LTOPF No.</Label>
                    <Input id="ltopf_no" type="text" class="uppercase" required autofocus v-model="form.ltopf_no" />
                    <InputError :message="form.errors.ltopf_no" />
                </div>
                <div class="grid gap-2">
                    <Label for="gender">Licensed Type</Label>
                    <SelectRoot v-model="form.license_type">
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
                                    <SelectLabel class="px-[25px] text-xs leading-[25px] font-medium"> Licensed Type </SelectLabel>
                                    <SelectGroup>
                                        <SelectItem
                                            v-for="(option, index) in licenseTypeOptions"
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
                    <InputError :message="form.errors.license_type" />
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
