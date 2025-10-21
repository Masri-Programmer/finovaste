// src/composables/useScrollSpy.ts
import { home } from '@/routes';
import { router } from '@inertiajs/vue3';
import { onMounted, onUnmounted, ref } from 'vue';

const activeSection = ref('hero');
const isScrollingProgrammatically = ref(false);
const observer = ref<IntersectionObserver | null>(null);

const handleScroll = (id: string) => {
    router.visit(home.url() + '#' + id, {
        preserveScroll: true,
        preserveState: true,
    });
    const element = document.getElementById(id);
    if (element) {
        isScrollingProgrammatically.value = true;
        activeSection.value = id;
        element.scrollIntoView({ behavior: 'smooth', block: 'start' });

        setTimeout(() => {
            isScrollingProgrammatically.value = false;
        }, 1000);
    }
};

export function initializeScrollSpy(sectionIds: string[]) {
    onMounted(() => {
        const elements = sectionIds.map((id) => document.getElementById(id)).filter((el): el is HTMLElement => el !== null);

        observer.value = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting && !isScrollingProgrammatically.value) {
                        activeSection.value = entry.target.id;
                    }
                });
            },
            {
                rootMargin: '-50% 0px -50% 0px',
                threshold: 0,
            },
        );

        elements.forEach((el) => observer.value?.observe(el));
    });

    onUnmounted(() => {
        if (observer.value) {
            observer.value.disconnect();
        }
    });
}

export function useScrollSpy() {
    return {
        activeSection,
        handleScroll,
    };
}
