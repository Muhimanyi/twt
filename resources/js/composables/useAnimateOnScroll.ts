import { ref, onMounted, onUnmounted, type Ref } from 'vue';

export function useAnimateOnScroll(threshold = 0.15) {
    const el = ref<HTMLElement>();
    const isVisible = ref(false);

    let observer: IntersectionObserver | null = null;

    onMounted(() => {
        if (!el.value) return;
        observer = new IntersectionObserver(
            ([entry]) => {
                if (entry.isIntersecting) {
                    isVisible.value = true;
                    observer?.unobserve(entry.target);
                }
            },
            { threshold }
        );
        observer.observe(el.value);
    });

    onUnmounted(() => {
        observer?.disconnect();
    });

    return { el, isVisible };
}

export function useCountUp(target: Ref<number>, duration = 2000) {
    const current = ref(0);

    let startTimestamp: number | null = null;
    let frameId: number;

    function animate(timestamp: number) {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        current.value = Math.floor(progress * target.value);
        if (progress < 1) {
            frameId = requestAnimationFrame(animate);
        }
    }

    function start() {
        startTimestamp = null;
        frameId = requestAnimationFrame(animate);
    }

    function reset() {
        cancelAnimationFrame(frameId);
        current.value = 0;
    }

    return { current, start, reset };
}
