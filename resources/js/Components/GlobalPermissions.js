import { usePage } from '@inertiajs/vue3'

export function GlobalPermissions() {
    const page = usePage()

    const can = (permission) => {
        return page.props.permissions?.includes(permission) ?? false
    }

    return { can }
}