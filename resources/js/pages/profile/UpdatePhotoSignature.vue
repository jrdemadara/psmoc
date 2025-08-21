<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { useForm } from '@inertiajs/vue3';
import { VueSignaturePad } from '@selemondev/vue3-signature-pad';
import { Info, LoaderCircle } from 'lucide-vue-next';
import { ToastAction, ToastDescription, ToastProvider, ToastRoot, ToastTitle, ToastViewport } from 'reka-ui';
import { nextTick, onUnmounted, PropType, ref } from 'vue';
import cameraSound from '../../../assets/audio/camera.mp3';

interface Data {
    photo: string;
    signature: string;
}

const props = defineProps({
    resubmit: {
        type: Boolean,
        required: true,
    },
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
    resubmit: props.resubmit,
    token: props.token,
    photo: null as File | null,
    signature: null as File | null,
});

const formErrorMessage = ref<string>('');

const submit = () => {
    form.post(route('update-photosignature'), {
        forceFormData: true,
        onSuccess: () => {
            popSuccessToast();
            formErrorMessage.value = '';
        },
        onError: (errors) => {
            console.log(errors);
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

const videoRef = ref<HTMLVideoElement | null>(null);
const photo = ref();
photo.value = props.data.photo ?? null;
const canvas = ref<HTMLCanvasElement | null>(null);
let stream: MediaStream | null = null;
const cameraState = ref<'idle' | 'active' | 'captured'>('idle');
const captureSoundSrc = cameraSound;

const startCamera = async () => {
    try {
        stream = await navigator.mediaDevices.getUserMedia({ video: true });
        cameraState.value = 'active';
        await nextTick();
        if (videoRef.value) {
            videoRef.value.srcObject = stream;
            await videoRef.value.play();
        }
    } catch (err) {
        console.error('Failed to access camera:', err);
    }
};

const capturePhoto = () => {
    if (videoRef.value && canvas.value) {
        const context = canvas.value.getContext('2d');
        if (context) {
            canvas.value.width = videoRef.value.videoWidth;
            canvas.value.height = videoRef.value.videoHeight;
            context.translate(canvas.value.width, 0);
            context.scale(-1, 1);
            context.drawImage(videoRef.value, 0, 0, canvas.value.width, canvas.value.height);

            photo.value = canvas.value.toDataURL('image/jpeg');

            const blob = dataURLToBlob(photo.value);
            form.photo = new File([blob], 'photo.jpeg', {
                type: 'image/jpeg',
            });

            const audioElement = document.querySelector('audio') as HTMLAudioElement;
            if (audioElement) audioElement.play();

            cameraState.value = 'captured';
            if (stream) {
                stream.getTracks().forEach((track) => track.stop());
            }
        }
    }
};

function dataURLToBlob(dataUrl: string): Blob {
    const byteString = atob(dataUrl.split(',')[1]);
    const mimeString = dataUrl.split(',')[0].split(':')[1].split(';')[0];
    const buffer = new ArrayBuffer(byteString.length);
    const uintArray = new Uint8Array(buffer);

    for (let i = 0; i < byteString.length; i++) {
        uintArray[i] = byteString.charCodeAt(i);
    }

    return new Blob([uintArray], { type: mimeString });
}

const state = ref({
    options: {
        penColor: 'rgb(0, 0, 0)',
        backgroundColor: 'rgb(255, 255, 255)',
    },
    disabled: false,
});

const signature = ref();
const changeSignature = ref(false);

const handleSave = (format = 'image/png') => {
    const base64 = signature.value.saveSignature(format);
    const blob = dataURLToBlob(base64);
    const file = new File([blob], 'signature.png', { type: blob.type });

    form.signature = file;
};

const handleClear = () => {
    return signature.value.clearCanvas();
};

onUnmounted(() => {
    if (stream) {
        stream.getTracks().forEach((track) => track.stop());
    }
});
</script>

<template>
    <div class="flex">
        <form @submit.prevent="submit" class="flex w-full flex-col space-y-10">
            <h4 class="text-xl font-semibold text-zinc-50">Photo & Signature</h4>

            <div class="grid grid-cols-1 gap-6 text-zinc-50 lg:flex lg:flex-col lg:items-start lg:justify-start lg:space-y-8">
                <!-- Photo Section -->
                <div class="grid gap-2">
                    <div class="flex items-center space-x-1 text-primary"><Info /><strong>Reminder:</strong></div>
                    <div class="rounded-lg border border-primary p-6 shadow-md">
                        <h2 class="mb-4 font-semibold">📸 Photo Submission Guidelines for ID</h2>
                        <ul class="list-inside list-disc space-y-2 text-sm">
                            <li>Take the photo in good lighting.</li>
                            <li>Use a <span class="font-semibold">clean, plain background</span> (preferably white or light-colored).</li>
                            <li>Ensure your face is clearly visible, with no filters or obstructions.</li>
                            <li>Do not wear hats, sunglasses, or anything covering the face.</li>
                            <li>Face the camera directly — centered and front-facing (like a passport photo).</li>
                        </ul>
                        <p class="mt-4 text-sm">This photo will be used for your official ID, so please make sure it is clear and professional.</p>
                    </div>

                    <Label for="photo">Photo</Label>

                    <div
                        v-if="cameraState === 'idle' || cameraState === 'captured'"
                        class="flex w-full flex-col overflow-hidden rounded-lg border-2 border-zinc-800"
                    >
                        <img :src="photo" alt="" />
                        <button @click="startCamera" type="button" class="flex h-12 w-full items-center justify-center bg-blue-500 hover:bg-blue-600">
                            Retake Photo
                        </button>
                    </div>

                    <div v-if="cameraState === 'active'" class="flex h-fit w-full flex-col overflow-hidden rounded-lg border-2 border-zinc-800">
                        <video ref="videoRef" autoplay playsinline class="h-full w-full scale-x-[-1]"></video>
                        <button
                            @click="capturePhoto"
                            type="button"
                            class="flex h-12 w-full items-center justify-center bg-blue-500 hover:bg-blue-600"
                        >
                            Capture
                        </button>
                    </div>

                    <canvas ref="canvas" class="hidden"></canvas>
                    <audio ref="captureSound" :src="captureSoundSrc"></audio>
                    <InputError :message="form.errors.photo" />
                </div>

                <!-- Signature Section -->
                <div class="grid gap-2">
                    <Label for="signature">Signature</Label>
                    <div class="flex w-full flex-col items-start justify-start overflow-hidden rounded-lg">
                        <div v-if="!changeSignature" class="w-full">
                            <img :src="props.data.signature ?? undefined" class="h-[200px] w-full" />
                        </div>
                        <div v-else>
                            <VueSignaturePad
                                ref="signature"
                                height="200px"
                                maxWidth="3"
                                :disabled="state.disabled"
                                :options="{
                                    penColor: state.options.penColor,
                                    backgroundColor: state.options.backgroundColor,
                                }"
                                @end-stroke="handleSave('image/png')"
                            />
                        </div>

                        <div class="flex w-full">
                            <InputError :message="form.errors.signature" />
                            <div class="flex w-full">
                                <button
                                    type="button"
                                    class="w-full bg-blue-500 p-2 font-bold text-zinc-50 hover:bg-blue-600"
                                    @click="changeSignature = true"
                                >
                                    Change
                                </button>
                                <button type="button" class="w-full bg-zinc-600 p-2 font-bold text-zinc-50 hover:bg-zinc-600" @click="handleClear">
                                    Clear
                                </button>
                            </div>
                        </div>
                    </div>
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
