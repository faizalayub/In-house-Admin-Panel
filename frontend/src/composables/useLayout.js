import { ref } from 'vue'

const title    = ref('')
const subtitle = ref('')

export function useLayout() {
    return {
        title,
        subtitle,
        setLayout(t, s = '') {
            title.value    = t
            subtitle.value = s
        },
    }
}
