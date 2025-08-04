<script setup lang="ts">
import AppLogoSecondary from '@/components/AppLogoSecondary.vue';
import { Link } from '@inertiajs/vue3';
import { Crosshair, Facebook, Instagram, MoveRight, Youtube } from 'lucide-vue-next';
import { PropType, ref } from 'vue';
import { Carousel, Navigation, Pagination, Slide } from 'vue3-carousel';
import 'vue3-carousel/carousel.css';
import PSMOCRulebookPDF from '../../assets/files/PSMOC-Rulebook.pdf';
import iASRulebookPDF from '../../assets/files/iAS-Rulebook.pdf';
import welcome from '../../assets/images/welcome.jpg';

const isOpen = ref(false);

const props = defineProps({
    gunClubs: {
        type: Array as PropType<{ id: number; name: string; logo: string }[]>,
        required: true,
    },
});

const gunClubImages = Array.from({ length: props.gunClubs.length }, (_, index) => ({
    id: index + 1,
    url: props.gunClubs[index].logo,
}));

const gunClubsConfig = {
    height: 200,
    itemsToShow: 4,
    gap: 5,
    autoplay: true,
};

const currentSlide = ref(0);

const slideTo = (nextSlide) => (currentSlide.value = nextSlide);

const galleryConfig = {
    itemsToShow: 1,
    wrapAround: true,
    slideEffect: 'fade',
    mouseDrag: false,
    touchDrag: false,
    height: 680,
    autoplay: 4000,
};

const thumbnailsConfig = {
    height: 180,
    itemsToShow: 6,
    wrapAround: true,
    touchDrag: false,
    gap: 10,
};

const galleryImages = Array.from({ length: 21 }, (_, index) => ({
    id: index + 1,
    url: new URL(`../../assets/images/gallery/${index + 1}.jpg`, import.meta.url).href,
}));

function openPSMOCPDF() {
    setTimeout(() => {
        window.open(PSMOCRulebookPDF, '_blank');
    }, 500);
}

function iASPDF() {
    setTimeout(() => {
        window.open(iASRulebookPDF, '_blank');
    }, 500);
}

const isRegisterOpen = ref(false);
const isRulebookOpen = ref(false);

function toggleRegisterDropdown() {
    isRegisterOpen.value = !isRegisterOpen.value;
}

function toggleRulebookDropdown() {
    isRulebookOpen.value = !isRulebookOpen.value;
}
</script>
<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <div class="max-w-screen-3xl mx-auto flex flex-col items-center bg-[#FDFDFC] text-[#1b1b18] dark:bg-black">
        <!-- Section that fills the screen -->
        <section class="relative h-screen w-full">
            <!-- Header -->
            <header class="absolute top-0 left-0 z-20 w-full bg-zinc-50 text-white dark:bg-black">
                <div class="flex flex-wrap items-center justify-between px-4 py-5 lg:px-16">
                    <!-- Logo -->
                    <div class="flex items-center space-x-2">
                        <img src="../../assets/images/logo.png" alt="Logo" class="h-14 w-auto" />
                        <AppLogoSecondary class="text-black dark:text-zinc-50" />
                    </div>

                    <!-- Hamburger -->
                    <button @click="isOpen = !isOpen" class="text-black focus:outline-none lg:hidden dark:text-zinc-50">
                        <svg v-if="!isOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <!-- Navigation -->
                    <nav
                        :class="[
                            'w-full flex-col items-start space-y-4 text-center text-sm font-semibold text-black capitalize lg:flex lg:w-auto lg:flex-row lg:items-center lg:justify-center lg:gap-5 lg:space-y-0 dark:text-zinc-50',
                            isOpen ? 'mt-5 flex' : 'hidden lg:flex',
                        ]"
                        class="transition-all duration-300 ease-in-out"
                    >
                        <div class="group relative">
                            <button @click="toggleRegisterDropdown" class="inline-block px-5 py-1.5 uppercase hover:text-primary">Register</button>
                            <div
                                class="absolute z-10 w-52 rounded bg-white text-[#1b1b18] shadow-lg dark:bg-zinc-900 dark:text-[#EDEDEC]"
                                :class="[
                                    isRegisterOpen ? 'block' : 'hidden',
                                    'group-hover:block', // still allows desktop hover
                                ]"
                            >
                                <Link :href="route('register-member')" class="block px-4 py-2 text-start hover:bg-gray-100 dark:hover:bg-primary"
                                    >New Member</Link
                                >
                                <Link :href="route('register-member')" class="block px-4 py-2 text-start hover:bg-gray-100 dark:hover:bg-primary"
                                    >Membership Renewal</Link
                                >
                                <Link :href="route('register-gunclub')" class="block px-4 py-2 text-start hover:bg-gray-100 dark:hover:bg-primary"
                                    >Gun Club</Link
                                >
                            </div>
                        </div>

                        <Link :href="route('home')" class="inline-block px-5 py-1.5 uppercase hover:text-red-500">Matches</Link>

                        <div class="group relative">
                            <button @click="toggleRulebookDropdown" class="inline-block px-5 py-1.5 uppercase hover:text-red-500">Rulebook</button>
                            <div
                                class="absolute z-10 min-w-[150px] rounded bg-white text-[#1b1b18] shadow-lg dark:bg-zinc-900 dark:text-[#EDEDEC]"
                                :class="[isRulebookOpen ? 'block' : 'hidden', 'group-hover:block']"
                            >
                                <a href="#" @click="openPSMOCPDF()" class="block px-4 py-2 text-start hover:bg-gray-100 dark:hover:bg-primary"
                                    >PSMOC Rulebook</a
                                >
                                <a href="#" @click="iASPDF()" class="block px-4 py-2 text-start hover:bg-gray-100 dark:hover:bg-primary"
                                    >ISMOC Rulebook</a
                                >
                            </div>
                        </div>

                        <a href="#gunclubs" class="inline-block px-5 py-1.5 uppercase hover:text-primary">Gun Clubs</a>
                        <a href="#gallery" class="inline-block px-5 py-1.5 uppercase hover:text-primary">Gallery</a>
                        <!-- <Link :href="route('login')" class="inline-block px-5 py-1.5 uppercase hover:text-primary">Match Officers</Link>
                        <Link :href="route('login')" class="inline-block px-5 py-1.5 uppercase hover:text-primary">About</Link> -->

                        <!-- Socials + CTA -->
                        <div class="flex flex-col items-center space-y-3 pt-4 lg:ml-8 lg:flex-row lg:space-y-0 lg:space-x-3 lg:pt-0">
                            <div class="flex space-x-3">
                                <a
                                    href="https://www.facebook.com/psmoc.main"
                                    target="_blank"
                                    class="rounded-full bg-primary p-3 hover:bg-primary/60 dark:bg-primary/40"
                                >
                                    <Facebook color="white" :size="22" />
                                </a>
                                <div class="rounded-full bg-primary p-3 hover:bg-primary/60 dark:bg-primary/40">
                                    <Instagram color="white" :size="22" />
                                </div>
                                <div class="rounded-full bg-primary p-3 hover:bg-primary/60 dark:bg-primary/40">
                                    <Youtube color="white" :size="22" />
                                </div>
                            </div>

                            <Link
                                :href="route('register-member')"
                                class="mt-2 hidden h-10 w-32 items-center justify-center rounded-full bg-primary font-bold text-white hover:bg-primary/80 lg:mt-0 lg:inline-flex"
                            >
                                <strong>Join Us</strong>
                            </Link>
                        </div>
                    </nav>
                </div>
            </header>

            <!-- Main Hero -->
            <div
                class="relative flex h-full w-full flex-col items-center justify-center bg-cover bg-center bg-no-repeat text-center text-white sm:bg-[length:120%] lg:bg-cover lg:pt-52"
                :style="`background-image: url(${welcome})`"
            >
                <!-- Gradient overlay for both light and dark modes -->
                <div class="absolute inset-0 z-0">
                    <!-- Light mode gradient -->
                    <div class="absolute inset-0 bg-gradient-to-t from-white via-white/80 to-white/20 dark:hidden"></div>

                    <!-- Dark mode gradient -->
                    <div class="absolute inset-0 hidden bg-gradient-to-t from-black via-black/80 to-black/20 dark:block"></div>
                </div>

                <div class="relative z-10 flex h-full w-full flex-col items-center justify-center rounded-lg px-5 lg:space-y-28 lg:px-16">
                    <div class="flex h-full flex-col items-center justify-center">
                        <img src="../../assets/images/header.svg" alt="" class="w-[680px]" />
                        <p class="text-xl font-bold tracking-wider text-zinc-800 lg:text-2xl dark:text-zinc-200">
                            Philippine Shooters and Match Officers Confederation
                        </p>
                        <p class="mt-5 text-xl tracking-wider text-zinc-700 lg:text-2xl dark:text-zinc-400">
                            No gimmicks. Just pure firepower. At PSMOC, we take shooting to the extreme — <br />
                            live matches, advanced setups, and raw intensity that hits you like recoil.
                        </p>

                        <Link
                            :href="route('register-member')"
                            class="mt-12 flex h-16 w-56 items-center justify-between space-x-2 rounded-full bg-primary px-5 hover:bg-primary/80"
                        >
                            <MoveRight color="white" :size="24" />
                            <span class="text-lg font-bold text-white">Join Us</span>
                            <Crosshair color="white" :size="24" class="animate-spin" />
                        </Link>
                    </div>

                    <div class="mb-5 flex w-full">
                        <div class="flex h-12 w-full items-end">
                            <div class="h-1 w-6 bg-zinc-200 dark:bg-zinc-800"></div>
                        </div>
                        <div class="flex h-12 w-full space-x-3">
                            <div class="h-2 w-2 animate-spin bg-zinc-200 dark:bg-zinc-800"></div>
                            <div class="h-2 w-2 bg-zinc-200 dark:bg-zinc-800"></div>
                        </div>
                        <div class="flex h-12 w-full items-center">
                            <div class="h-6 w-6 bg-zinc-200 dark:bg-zinc-800"></div>
                        </div>
                        <div class="flex h-12 w-full items-end">
                            <div class="h-1 w-4 animate-bounce bg-zinc-200 dark:bg-zinc-800"></div>
                        </div>
                        <div class="flex h-12 w-full items-start">
                            <div class="h-1 w-32 bg-zinc-200 dark:bg-zinc-800"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section A -->
        <section class="flex w-full flex-col items-center justify-center px-5 lg:px-16">
            <h1 class="text-center text-4xl font-bold lg:text-7xl dark:text-zinc-50">Competitive Shooting Matches</h1>
            <div class="mt-5 flex w-full">
                <div class="flex h-12 w-full items-end"></div>
                <div class="flex h-12 w-full space-x-3"></div>
                <div class="flex h-12 w-full items-center"></div>
                <div class="flex h-12 w-full items-end"></div>
                <div class="flex h-12 w-full items-start">
                    <div class="h-6 w-6 animate-spin bg-zinc-200 dark:bg-zinc-800"></div>
                </div>
            </div>
            <div class="grid w-full grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                <div class="flex h-52 flex-col items-start justify-center space-y-3 rounded-lg border border-zinc-300 px-5 dark:border-zinc-400">
                    <div class="flex size-12 items-center justify-center rounded-full bg-primary p-2 text-3xl font-black text-zinc-50">1</div>
                    <h4 class="text-2xl font-bold text-zinc-800 dark:text-zinc-50">Nationwide Competition</h4>
                    <p class="text-xl text-zinc-600 dark:text-zinc-400">
                        Open to participants from all over the country. Join and showcase your skills.
                    </p>
                </div>

                <div class="flex h-52 flex-col items-start justify-center space-y-3 rounded-lg bg-primary px-5">
                    <div class="flex size-12 items-center justify-center rounded-full bg-zinc-50 p-2 text-3xl font-black text-zinc-950">2</div>
                    <h4 class="d text-2xl font-bold text-zinc-50">Elite Match Officers</h4>
                    <p class="text-xl text-zinc-100">Led by top-tier professionals ensuring fairness and precision in every match.</p>
                </div>

                <div class="flex h-52 flex-col items-start justify-center space-y-3 rounded-lg border border-zinc-300 px-5 dark:border-zinc-400">
                    <div class="flex size-12 items-center justify-center rounded-full bg-primary p-2 text-3xl font-black text-zinc-50">3</div>
                    <h4 class="text-2xl font-bold text-zinc-800 dark:text-zinc-50">Live-Fire Challenges</h4>
                    <p class="text-xl text-zinc-600 dark:text-zinc-400">
                        Experience intense, real-world scenarios that push your skills to the limit.
                    </p>
                </div>
            </div>
        </section>

        <!-- Section B -->
        <section class="mt-16 flex w-full flex-col items-center justify-center px-5 lg:px-16">
            <div class="mt-5 flex h-24 w-full">
                <div class="flex h-full w-full flex-col items-start justify-end">
                    <div class="h-1 w-24 bg-zinc-200 dark:bg-zinc-800"></div>
                    <div class="mt-4 h-1 w-32 bg-zinc-200 dark:bg-zinc-800"></div>
                </div>
                <div class="flex h-full w-full items-center justify-center space-x-3">
                    <div class="h-6 w-6 animate-pulse bg-zinc-200 dark:bg-zinc-800"></div>
                </div>
                <div class="flex h-full w-full items-start justify-center">
                    <div class="h-2 w-2 bg-zinc-200 dark:bg-zinc-800"></div>
                </div>
                <div class="flex h-full w-full items-end justify-center space-x-2">
                    <div class="h-1 w-1 bg-zinc-200 dark:bg-zinc-800"></div>
                    <div class="h-1 w-1 bg-zinc-200 dark:bg-zinc-800"></div>
                </div>
                <div class="flex h-full w-full items-end justify-end">
                    <div class="h-4 w-4 bg-zinc-200 dark:bg-zinc-800"></div>
                </div>
            </div>
            <div class="mt-10 flex w-full flex-col items-center gap-8 lg:flex-row">
                <!-- Image Wrapper -->
                <div class="flex-shrink-0">
                    <img src="../../assets/images/agila.png" alt="" class="h-auto w-72 object-contain lg:w-[600px]" />
                </div>

                <!-- Text Content -->
                <div class="flex flex-col lg:flex-row">
                    <div class="flex items-start space-x-3 lg:justify-center">
                        <img src="../../assets/images/qoute.svg" alt="" class="h-auto w-8 object-contain lg:w-32" />
                        <img src="../../assets/images/qoute.svg" alt="" class="h-auto w-8 object-contain lg:w-96" />
                    </div>

                    <div class="mt-0 flex flex-col lg:mt-32">
                        <p class="text-justify text-xl leading-normal tracking-wider text-black dark:text-zinc-50">
                            Dear PSMOC Family members, <br /><br />
                            It is with great honor and pride that I announce the creation of the International Shooters and Match Officers
                            Confederation (ISMOC), Inc.—the first international shooting organization founded and headquartered in the Philippines.
                            ISMOC will govern and sanction matches globally through affiliated clubs and associations. Its training arm, IMOO, will
                            develop world-class Match Officers to officiate worldwide events. This milestone is a result of the unwavering dedication
                            of our officers, Match Officers, members, and staff—thank you all. As reflected in our logo, ISMOC thrives with dynamic
                            energy, united by a shared passion for the shooting sport. Mabuhay!
                        </p>
                        <span class="mt-5 text-black dark:text-zinc-50">
                            <strong class="">CONG. SUHARTO “Teng” MANGUDADATU, Ph.D.</strong><br />
                            Representative, 1st District of Sultan Kudarat, <br />
                            Philippines President, International Shooters and Match Officers Confederation President, <br />
                            Philippine Shooters and Match Officers Confederation
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section Gun Clubs -->
        <section id="gunclubs" class="mt-32 flex w-full flex-col items-center justify-center px-5 lg:px-16">
            <h1 class="text-center text-4xl font-bold lg:text-7xl dark:text-zinc-50">Registered Gun Clubs</h1>
            <p class="mx-auto mt-5 max-w-6xl text-center text-lg tracking-wider text-zinc-600 lg:text-2xl dark:text-zinc-400">
                Discover gun clubs officially registered under ISMOC, representing excellence in sportsmanship, safety, and competition across the
                Philippines and beyond.
            </p>
            <div class="mt-5 flex w-full">
                <div class="flex h-12 w-full items-end">
                    <div class="h-1 w-1 bg-zinc-200 dark:bg-zinc-800"></div>
                    <div class="h-1 w-1 bg-zinc-200 dark:bg-zinc-800"></div>
                </div>
                <div class="flex h-12 w-full space-x-3"></div>
                <div class="flex h-12 w-full items-center">
                    <div class="mt-4 h-1 w-32 bg-zinc-200 dark:bg-zinc-800"></div>
                </div>
                <div class="flex h-12 w-full items-end"></div>
                <div class="flex h-12 w-full items-start">
                    <div class="h-6 w-6 animate-spin bg-zinc-200 dark:bg-zinc-800"></div>
                </div>
            </div>
            <Carousel v-bind="gunClubsConfig">
                <Slide v-for="image in gunClubImages" :key="image.id">
                    <div class="carousel__item">
                        <img :src="image.url" alt="image" />
                    </div>
                </Slide>

                <template #addons>
                    <Navigation />
                    <Pagination />
                </template>
            </Carousel>
        </section>

        <!-- Section Gallery -->
        <section id="gallery" class="mt-32 flex w-full flex-col items-center justify-center px-5 lg:px-16">
            <h1 class="text-center text-4xl font-bold lg:text-7xl dark:text-zinc-50">Gallery</h1>
            <p class="mx-auto mt-5 max-w-6xl text-center text-lg tracking-wider text-zinc-600 lg:text-2xl dark:text-zinc-400">
                A curated collection of memorable events, trainings, and activities showcasing the spirit and dedication of our shooting community.
            </p>
            <div class="mt-5 flex w-full">
                <div class="flex h-full w-full items-start justify-center">
                    <div class="h-2 w-2 bg-zinc-200 dark:bg-zinc-800"></div>
                </div>
                <div class="flex h-12 w-full space-x-3"></div>
                <div class="flex h-12 w-full items-center">
                    <div class="h-2 w-2 bg-zinc-200 dark:bg-zinc-800"></div>
                </div>
                <div class="flex h-12 w-full items-end"></div>
                <div class="flex h-full w-full items-end justify-center space-x-2">
                    <div class="h-1 w-1 bg-zinc-200 dark:bg-zinc-800"></div>
                    <div class="h-1 w-1 bg-zinc-200 dark:bg-zinc-800"></div>
                </div>
            </div>
            <Carousel id="gallery" v-bind="galleryConfig" v-model="currentSlide">
                <Slide v-for="image in galleryImages" :key="image.id">
                    <img :src="image.url" alt="Gallery Image" class="gallery-image" />
                </Slide>
            </Carousel>

            <Carousel id="thumbnails" v-bind="thumbnailsConfig" v-model="currentSlide">
                <Slide v-for="image in galleryImages" :key="image.id">
                    <template #default="{ currentIndex, isActive }">
                        <div :class="['thumbnail', { 'is-active': isActive }]" @click="slideTo(currentIndex)">
                            <img :src="image.url" alt="Thumbnail Image" class="thumbnail-image" />
                        </div>
                    </template>
                </Slide>

                <template #addons>
                    <Navigation />
                </template>
            </Carousel>
        </section>

        <!-- Section Gallery -->
        <section class="mt-32 flex w-full flex-col items-center justify-center px-5 lg:px-16">
            <div class="mb-5 flex w-full">
                <div class="flex h-12 w-full items-end">
                    <div class="h-1 w-6 bg-zinc-200 dark:bg-zinc-800"></div>
                </div>
                <div class="flex h-12 w-full space-x-3">
                    <div class="h-2 w-2 animate-spin bg-zinc-200 dark:bg-zinc-800"></div>
                    <div class="h-2 w-2 bg-zinc-200 dark:bg-zinc-800"></div>
                </div>
                <div class="flex h-12 w-full items-center">
                    <div class="h-6 w-6 bg-zinc-200 dark:bg-zinc-800"></div>
                </div>
                <div class="flex h-12 w-full items-end">
                    <div class="h-1 w-4 animate-bounce bg-zinc-200 dark:bg-zinc-800"></div>
                </div>
                <div class="flex h-12 w-full items-start">
                    <div class="h-1 w-32 bg-zinc-200 dark:bg-zinc-800"></div>
                </div>
            </div>
            <div
                class="flex w-full flex-col justify-center rounded-tl-4xl rounded-br-4xl bg-zinc-100 px-5 py-5 shadow shadow-zinc-200 lg:p-32 dark:bg-zinc-800 dark:shadow-zinc-900"
            >
                <h1 class="text-center text-2xl font-bold lg:text-start lg:text-5xl dark:text-zinc-50">Check Our Facebook Page</h1>
                <div class="mt-6 flex flex-col-reverse lg:flex-row">
                    <div class="flex flex-col items-center space-y-5 space-x-5 lg:items-start">
                        <p class="mt-5 max-w-2xl text-center text-lg md:text-start lg:mt-0 lg:text-xl dark:text-zinc-300">
                            Stay connected with the Philippine Shooters and Match Officers Confederation (PSMOC) through our official Facebook page.
                            Get the latest updates on upcoming events, training sessions, match results, and behind-the-scenes action straight from
                            the range. Whether you're a competitive shooter, a range officer, or just passionate about the sport, our Facebook
                            community is the place to be. Follow us now and be part of the growing force that's redefining shooting sports in the
                            Philippines.
                        </p>
                        <a
                            href="https://www.facebook.com/psmoc.main"
                            target="_blank"
                            class="mt-5 w-fit items-center justify-center rounded-full bg-primary px-8 py-5 font-bold text-white hover:bg-primary/80 lg:mt-0 lg:inline-flex"
                        >
                            <strong>Follow us on Facebook</strong>
                        </a>
                    </div>
                    <div class="aspect-w-16 aspect-h-9">
                        <video class="h-auto w-full rounded-lg shadow-lg" controls playsinline>
                            <source src="../../assets/videos/1.mp4" type="video/mp4" />
                            Your browser does not support the video tag.
                        </video>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="flex w-full flex-col items-center justify-center py-5">
            <div class="flex items-center justify-center px-16 dark:text-zinc-50">Copyright © 2025 PSMOC. All Rights Reserved.</div>
        </footer>
    </div>
</template>

<style scoped>
:root {
    background-color: #242424;
}

.carousel {
    --vc-pgn-background-color: rgba(255, 255, 255, 0.7);
    --vc-pgn-active-color: rgba(255, 255, 255, 1);
    --vc-nav-background: rgba(255, 255, 255, 0.7);
    --vc-nav-border-radius: 100%;
}

.gallery-image {
    border-radius: 16px;
}

#thumbnails {
    margin-top: 10px;
}

.thumbnail {
    height: 100%;
    width: 100%;
    cursor: pointer;
    opacity: 0.6;
    transition: opacity 0.3s ease-in-out;
}

.thumbnail.is-active,
.thumbnail:hover {
    opacity: 1;
}
</style>
