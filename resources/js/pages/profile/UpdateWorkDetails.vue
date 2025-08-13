<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { ToastAction, ToastDescription, ToastProvider, ToastRoot, ToastTitle, ToastViewport } from 'reka-ui';
import { PropType, ref } from 'vue';

interface Data {
    occupation: string;
    company_organization: string | '';
    position: string | '';
    office_business_address: string | '';
    office_landline: string | '';
    office_email: string | '';
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

const form = useForm({
    token: props.token,
    occupation: props.data.occupation,
    company_organization: props.data.company_organization,
    position: props.data.position,
    office_business_address: props.data.office_business_address,
    office_landline: props.data.office_landline,
    office_email: props.data.office_email,
});

const formErrorMessage = ref<string>('');

const submit = () => {
    form.patch(route('update-work-details'), {
        onSuccess: () => {
            popSuccessToast();
            formErrorMessage.value = '';
        },
        onError: (errors) => {
            formErrorMessage.value = Object.values(errors)[0]?.[0] ?? 'Something went wrong';
        },
    });
};

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
            <h4 class="text-xl font-semibold text-zinc-50">Personal Details</h4>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                <div class="grid gap-2">
                    <Label for="occupation">Occupation</Label>
                    <Input id="occupation" type="text" class="capitalize" required autofocus autocomplete="occupation" v-model="form.occupation" />
                    <InputError :message="form.errors.occupation" />
                </div>
                <div class="grid gap-2">
                    <Label for="company_organization">Company/Organization</Label>
                    <Input
                        id="company_organization"
                        type="text"
                        class="capitalize"
                        autofocus
                        autocomplete="purok"
                        v-model="form.company_organization"
                    />
                    <InputError :message="form.errors.company_organization" />
                </div>
                <div class="grid gap-2">
                    <Label for="position">Position</Label>
                    <Input id="position" type="text" class="capitalize" required autofocus autocomplete="position" v-model="form.position" />
                    <InputError :message="form.errors.position" />
                </div>
                <div class="grid gap-2">
                    <Label for="office_business_address">Office/Business</Label>
                    <Input
                        id="office_business_address"
                        type="text"
                        class="capitalize"
                        autofocus
                        autocomplete="office_business_address"
                        v-model="form.office_business_address"
                    />
                    <InputError :message="form.errors.office_business_address" />
                </div>

                <div class="grid gap-2">
                    <Label for="office_landline">Landline</Label>
                    <Input id="office_landline" type="tel" autofocus autocomplete="office_landline" v-model="form.office_landline" />
                    <InputError :message="form.errors.office_landline" />
                </div>

                <div class="grid gap-2">
                    <Label for="office_email">Email</Label>
                    <Input id="office_email" type="email" autofocus autocomplete="office_email" v-model="form.office_email" />
                    <InputError :message="form.errors.office_email" />
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
