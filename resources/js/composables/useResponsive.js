import { MOBILE_BREAKPOINT } from '@/config/constants';
import { ref, onMounted, onUnmounted } from 'vue';

// 画面サイズ監視
export function useResponsive() {
    const isMobile = ref(window.innerWidth < MOBILE_BREAKPOINT);

    const updateDeviceType = () => {
        isMobile.value = window.innerWidth < MOBILE_BREAKPOINT;
    };

    onMounted(() => {
        window.addEventListener('resize', updateDeviceType);
    });
    onUnmounted(() => {
        window.removeEventListener('resize', updateDeviceType);
    });

    return { isMobile };
}
